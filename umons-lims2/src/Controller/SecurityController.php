<?php

namespace App\Controller;

use App\Repository\UserRepository;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/admin/login", name="login")
     */
    public function adminLogin(AuthenticationUtils $authenticationUtils): Response
    {

        if ($this->getUser() && $this->isGranted("ROLE_ADMIN") && $this->isGranted(new Expression(
                "token.hasAttribute('ULIMS:IS_USED_ADMIN_AUTH') and token.getAttribute('ULIMS:IS_USED_ADMIN_AUTH') == true"
            ))) {
            return $this->redirectToRoute('login');

        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);


    }

    /**
     * @Route("/userselect", name="userselect")
     */
    public function userSelect(Request $request, UserRepository $userRepository)
    {

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


}
