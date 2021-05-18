<?php
// src/Controller/MainController.php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\Usage;
use App\Entity\User;
use App\Repository\ProductRepository;
use http\Env\Request;
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
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();
        return $this ->render('login.html.twig', ['users' => $users]);
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

        return $this ->render('product.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/changeProduct/{action}/{product}", name="changeProduct")
     */
    public function changeProduct(int $action, int $product): Response
    {
        $en = $this->getDoctrine()->getManager();

        $pr = $this->getDoctrine()->getRepository(Product::class);
        $prod = $pr->find($product);

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find(1);

        $usage = new Usage();
        $usage->setAction($action);
        $usage->setDate(new \DateTime());
        $usage->setProduct($prod);
        $usage->setUser($user);
        $this->generateUrl('product');

        $en->persist($usage);

        $en->flush();

        return $this->redirectToRoute("product");
    }
    
}