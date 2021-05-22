<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function adminLogin(Request $request, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder,  AuthenticationUtils $authenticationUtils): Response
    {



        if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin.index');
        }

//        if ($request->isMethod('POST')) {
//            $password =  $request->request->get('registration_number');
//            $csrf_token = $request->request->get('_csrf_token');
//
//            $token = new CsrfToken('authenticate-simple', $csrf_token);
//            if (!$csrfTokenManager->isTokenValid($token)) {
//                throw new InvalidCsrfTokenException();
//            }
//
//            if($passwordEncoder->isPasswordValid($this->getUser(), $password)) {
//                $this->getUser()->setIsAdminAllowed(true);
//                return $this->redirectToRoute('admin.index');
//            }
//
//            $this->addFlash('error', 'Mot de passe incorrect.');
//
//        } else {
//
//        }

        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('admin_login.html.twig', [
            'error'=>$error
        ]);


    }

    /**
     * @Route("/userselect", name="userselect")
     */
    public function userSelect(Request $request, CsrfTokenManagerInterface $csrfTokenManager, UserRepository $userRepository): Response
    {

        if ($request->query->get("type")== 'full' && $this->getUser()) {
            if($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin.index');
            }

            if($this->isGranted('ROLE_USER')) {
                return $this->redirectToRoute('login');
            }

        }


        $users = $userRepository->findAll();
        return $this ->render('userchoose.html.twig', ['users' => $users]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
