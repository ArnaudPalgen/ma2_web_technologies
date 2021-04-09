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
        $users=array("Jean", "Jeanne", "Marc", "Jerome", "Paul", "Thomas", "Jaqueline", "Jilbert", "Jean-Jacques");
        return $this ->render('login.html.twig', ['users' => $users]);
    }
	
	/**
    * @Route("/product")
    */
    public function product(): Response
    {
        $product=array(
			array("used" => True, "cas" => "331-39-5", "name" => "Caffeic acid", "location" => "A 3", "concentration" => "80%", "open" => True),
			array("used" => True, "cas" => "50-78-2", "name" => "Aspirin", "location" => "B 6", "concentration" => "80%", "open" => True),
			array("used" => False, "cas" => "331-39-5", "name" => "Caffeic acid", "location" => "A 3", "concentration" => "60%", "open" => True),
			array("used" => False, "cas" => "57-27-2", "name" => "Morphine", "location" => "D 5", "concentration" => "90%", "open" => False),
			array("used" => False, "cas" => "100-52-7", "name" => "Benzaldehyde", "location" => "A 3", "concentration" => "80%", "open" => True)
			);
		
		
        return $this ->render('product.html.twig', ['product' => $product]);
    }
    
}