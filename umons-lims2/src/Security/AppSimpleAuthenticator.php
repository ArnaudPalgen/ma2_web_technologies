<?php


namespace App\Security;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\HttpUtils;
use function strlen;

class AppSimpleAuthenticator extends AbstractLoginFormAuthenticator
{

    private $httpUtils;
    private $userProvider;

    private $login_path = "userselect";
    private $check_path = "userselect";

    private $on_success_redirect_route = "products.index";

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

        $user = $this->userProvider->loadUserByUsername($credentials['username']);
//        $user->setIsAdminAllowed(false);

        if (!$user instanceof UserInterface) {
            throw new AuthenticationServiceException('The user provider must return a UserInterface object.');
        }
        return new SelfValidatingPassport(new UserBadge($user->getUsername()), [new RememberMeBadge()]);
    }

    private function getCredentials(Request $request): array
    {
        $credentials = [
            'username' => $request->request->get('_username'),
        ];

        if (strlen($credentials['username']) > Security::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Invalid username.');
        }

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

        return $credentials;
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