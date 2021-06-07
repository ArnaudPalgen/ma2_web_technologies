<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/admin', name: 'admin.')]
class AdminController extends AbstractController
{



    #[Route('/manageUser', name: 'user')]
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        $repository_role = $this->getDoctrine()->getRepository(Role::class);
        $roles = $repository_role->findAll();

        return $this->render('admin.html.twig', ['users' => $users, 'roles' => $roles]);
    }


    #[Route('/changeUser/{id}/{registration_number}/{first_name}/{last_name}/{mdp}/{role}/{action}', name: 'change.user')]
    public function changeUser(int $id, int $registration_number, string $first_name, string $last_name, string $mdp, string $role, string $action, UserPasswordEncoderInterface $encoder): Response
    {
        $en = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);


        if (!$user) {
            return $this->json(json_encode(false));
        }

        if($action === "remove"){
            $user->setDeletedAt(new DateTime());
        }else{
            $newRole = $this->getDoctrine()->getRepository(Role::class)->findOneBy(array('name' => [$role]));

            if ($mdp !== '') {
                $user->setPassword(
                    $encoder->encodePassword(
                        $user,
                        $mdp
                    )
                );
            }
            $user->setFirstName($first_name);
            $user->setLastName($last_name);
            $user->setRegistrationNumber($registration_number);
            $user->setRole($newRole);
        }



        $en->flush();


        return $this->json(json_encode(true));
    }


}
