<?php
// src/Controller/MainController.php
namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @Route("/product-create")
     */
    public function createProduct(): Response
    {
        $units = [
            "g" => "g",
            "mg" => "mg",
            "kg" => "kg",
            "μg" => "μg",
            "mL" => "mL",
            "L" => "L",
            "μl" => "μl",
            "mmol" => "mmol",
        ];
        $form = $this->createFormBuilder()
            ->add('product_name', TextType::class, ['label' => 'Nom du produit'])
            ->add('cas_number', TextType::class, ['label' => 'Numéro CAS'])
            ->add('concentration', TextType::class, ['label' => 'Concentration'])
            ->add('shelf', TextType::class, ['label' => 'Etagère'])
            ->add('level', TextType::class, ['label' => 'Niveau'])
            ->getForm();

        return $this->render('product_create.html.twig', [
            'form' => $form->createView(),
        ]);
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