<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\AppAdminAuthenticator;
use App\Security\AppCustomAuthenticator;
use App\Security\DummyAuthenticator;
use App\Security\TestAuthenticator;
use ContainerIttvJY5\getFormLoginAuthenticatorService;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class SecurityController extends AbstractController
{
    /**
     * @Route("/admin/login", name="login")
     */
    public function adminLogin(AuthenticationUtils $authenticationUtils): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin_login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/userselect", name="userselect")
     */
    public function userSelect(Request $request, UserRepository $userRepository
//        , UserAuthenticatorInterface $authenticator,TestAuthenticator $formAuthenticator

    ,AppAdminAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler
    )
    {
        if ($request->isMethod("POST")) {

            $registrationNumber = $request->request->get('_username');

            $user = $userRepository->findOneBy(['registration_number' =>$registrationNumber ]);


            if (!$user) {
                $this->addFlash('danger', 'Utilisateur non trouvé.');
            } else {
                $user->setIsAdminAllowed(false);

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,          // the User object you just created
                    $request,
                    $authenticator, // authenticator whose onAuthenticationSuccess you want to use
                    'main'          // the name of your firewall in security.yaml
                );

//                return $authenticator->authenticateUser(
//                    $user,
//                    $formAuthenticator,
//                    $request);
            }
        }
        $redirect_route = $request->query->get("redirect_route");


        $users = $userRepository->findAll();
        return $this->render('userchoose.html.twig', [
            'users' => $users,
            'redirect_route' => $redirect_route
        ]);



    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/admin/login", name="login.check")
     */
    public function loginCheck()
    {
        throw new LogicException('This method can be blank - it will be used by admin authenticator.');
    }
}
