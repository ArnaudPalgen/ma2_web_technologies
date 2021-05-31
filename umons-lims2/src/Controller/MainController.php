<?php
// src/Controller/MainController.php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{


    /**
     * @Route("/lucky/number")
     */
    public function number(): Response
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>Lucky number: ' . $number . '</body></html>'
        );
    }

    /**
     * @Route("/product", name="product")
     */
    public function product(): Response
    {


        $pr = $this->getDoctrine()->getRepository(Product::class);
        $products = $pr->getProductList();


        //$date = new \DateTime();
        //$date->setTimezone(new \DateTimeZone('Europe/Paris'));
        //$date = date_format($date, 'Y-m-d H:i:s');

        return $this->render('product.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/adminTest")
     */
    public function admin(): Response
    {

        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        $repository_role = $this->getDoctrine()->getRepository(Role::class);
        $roles = $repository_role->findAll();

        return $this->render('admin.html.twig', ['users' => $users, 'roles' => $roles]);
    }


    /**
     * @Route("/login")
     */
    public function login(): Response
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();
        return $this->render('login.html.twig', ['users' => $users]);
    }


}