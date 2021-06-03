<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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


    #[Route('/changeUser/{id}/{registration_number}/{first_name}/{last_name}/{role}/{action}', name: 'change.user')]
    public function changeUser(int $id, int $registration_number, string $first_name, string $last_name, string $role, string $action): Response
    {
        $en = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);


        if (!$user) {
            return $this->json(json_encode(false));
        }

        if($action === "remove"){
            $en->remove($user);
        }else{
            $newRole = $this->getDoctrine()->getRepository(Role::class)->findOneBy(array('name' => [$role]));
            $user->setFirstName($first_name);
            $user->setLastName($last_name);
            $user->setRegistrationNumber($registration_number);
            $user->setRole($newRole);
        }



        $en->flush();


        return $this->json(json_encode(true));
    }



    #[Route('/addUser/{registration_number}/{first_name}/{last_name}/{role}', name: 'add.user')]
    public function addUser(int $registration_number, string $first_name, string $last_name, string $role): Response
    {
        $en = $this->getDoctrine()->getManager();

        $newRole = $this->getDoctrine()->getRepository(Role::class)->findOneBy(array('name' => [$role]));

        $user = new User();

        $user->setFirstName($first_name);
        $user->setLastName($last_name);
        $user->setRegistrationNumber($registration_number);
        $user->setRole($newRole);

        $en->persist($user);

        $en->flush();

        return $this->json(json_encode(true));
    }

}
