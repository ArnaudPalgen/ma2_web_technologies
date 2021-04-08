<?php
// src/Controller/MainController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/login")
     */
    public function login(): Response
    {
        $users=array("Jean", "Jeanne", "Marc", "Jerome", "Paul", "Thomas", "Jaqueline", "Jilbert", "Jean-Jacques", "Fe", "Rochell", "Angelique", "Tawanna", "Alyce", "Delmy", "Merna", "Abbey", "Kaitlin", "America", "Aundrea", "Theresia", "Luana", "Annie", "Arleen", "Karoline", "Gus", "Edris", "Elenora", "Lurlene", "Kelsie", "Jospeh", "Ed", "Nadine", "Irina", "Samara", "Mamie", "Shaniqua", "Glayds", "Jerric");
        return $this ->render('login.html.twig', ['users' => $users]);
    }
    
}