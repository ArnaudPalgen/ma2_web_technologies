<?php


namespace App\Security;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\HttpUtils;
use function strlen;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{

    private HttpUtils $httpUtils;
    private UserProviderInterface $userProvider;

    private string $login_path = "login";
    private string $check_path = "login";

    private string $on_success_redirect_route = "admin.user";

    public function __construct(HttpUtils $httpUtils, UserProviderInterface $userProvider)
    {
        $this->httpUtils = $httpUtils;
        $this->userProvider = $userProvider;


    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $this->httpUtils->checkRequestPath($request, $this->check_path);
    }

    public function authenticate(Request $request): PassportInterface
    {
        $credentials = $this->getCredentials($request);

        $passport = new Passport(new UserBadge($credentials['username'], function ($username) {
            $user = $this->userProvider->loadUserByUsername($username);
            if (!$user instanceof UserInterface) {
                throw new AuthenticationServiceException('The user provider must return a UserInterface object.');
            }


            return $user;
        }), new PasswordCredentials($credentials['password']), [new RememberMeBadge()]);


        if ($this->userProvider instanceof PasswordUpgraderInterface) {
            $passport->addBadge(new PasswordUpgradeBadge($credentials['password'], $this->userProvider));
        }

        return $passport;
    }

    private function getCredentials(Request $request): array
    {
        $credentials = [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        ];

        if (strlen($credentials['username']) > Security::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Invalid username.');
        }

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }

    /**
     * @param Passport $passport
     */
    public function createAuthenticatedToken(PassportInterface $passport, string $firewallName): TokenInterface
    {
        $usernamePasswordToken = new UsernamePasswordToken($passport->getUser(), null, $firewallName, $passport->getUser()->getRoles());
        $usernamePasswordToken->setAttribute('ULIMS:IS_USED_ADMIN_AUTH', true);
        return $usernamePasswordToken;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($redirect_route = $request->request->get('_redirect_route')) {
            return $this->httpUtils->createRedirectResponse($request, $redirect_route);
        }
        if ($redirect_route = $request->getSession()->get('_security.' . $firewallName . '.target_path')) {
            return $this->httpUtils->createRedirectResponse($request, $redirect_route);
        }
        return $this->httpUtils->createRedirectResponse($request, $this->on_success_redirect_route);

    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->httpUtils->generateUri($request, $this->login_path);
    }


}