<?php

namespace App\Controller;

use App\Repository\UserRepository;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function adminLogin(AuthenticationUtils $authenticationUtils): Response
    {

        if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin.index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('admin_login.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Route("/userselect", name="userselect")
     */
    public function userSelect(Request $request, UserRepository $userRepository): Response
    {

        if ($request->query->get("type") == 'full' && $this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin.index');
            }

            if ($this->isGranted('ROLE_USER')) {
                return $this->redirectToRoute('login');
            }

        }


        $users = $userRepository->findAll();
        return $this->render('userchoose.html.twig', ['users' => $users]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
