<?php
// src/Controller/MainController.php
namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController{
    /**
     * @Route("/lucky/number")
     */
    public function number(): Response
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }


    /**
     * @Route("/admin")
     */
    public function admin(): Response
    {

        $users=[
            [ "first_name" => "Kirk", "last_name" => "Christensen"],
            [ "first_name" => "Olivia", "last_name" => "Adam"],
            [ "first_name" => "Fraya", "last_name" => "Mohammed"],
            [ "first_name" => "Parker", "last_name" => "Cleveland"],
            [ "first_name" => "Cinar", "last_name" => "Warren"],
            [ "first_name" => "Martine", "last_name" => "Harrington"],
            [ "first_name" => "Felix", "last_name" => "Campos"],
            [ "first_name" => "Eliana", "last_name" => "Cameron"],
            [ "first_name" => "Brooke", "last_name" => "Coates"],
            [ "first_name" => "Saniya", "last_name" => "Hernandez"],
        ];


        return $this ->render('admin.html.twig', ['users' => $users]);
    }

    
}