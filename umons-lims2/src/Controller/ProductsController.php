<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Usage;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{



    #[Route('/', name: 'products.index')]
    public function index(): Response
    {

        $pr = $this->getDoctrine()->getRepository(Product::class);
        $products = $pr->getProductList();


        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/changeProduct/{action}/{product}', name: 'products.change')]
    public function changeProduct(int $action, int $product): Response
    {
        $en = $this->getDoctrine()->getManager();

        $pr = $this->getDoctrine()->getRepository(Product::class);
        $prod = $pr->find($product);

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $this -> getUser();
        $nameUser = $this -> getUser() -> getFullName();

        $usage = new Usage();
        $usage->setAction($action);
        $usage->setDate(new \DateTime());
        $usage->setProduct($prod);
        $usage->setUser($user);
        $this->generateUrl('product');

        $en->persist($usage);

        $en->flush();

        return $this->json(json_encode($nameUser));
    }

    #[Route(path :"new_product", name: 'products.create')]
    public function new(Request $request): Response {

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $product = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté.');

            return $this->redirectToRoute('products.index');
        }

        return $this->render('product_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }





    #[Route('/products/json/search', name: 'products.search.json')]
    public function jsonSearch(Request $request, ProductRepository $productRepository): Response {

        $query = $request->query->get('q');
        if($query) {
            $res =  $productRepository->findByNCAS($query);
            return $this->json($res);
        }
        return $this->json(null, 204);
    }

    /**
     * @Route("/history")
     */
    public function history(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Usage::class);
        $history = $repository->findAll();
        return $this->render('history.html.twig', ['history' => $history, ]);
    }


}
