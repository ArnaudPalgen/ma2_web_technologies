<?php

namespace App\Controller;

use App\Entity\Name;
use App\Entity\Product;
use App\Entity\Usage;
use App\Form\ProductType;
use App\Repository\NameRepository;
use App\Repository\ProductRepository;
use App\Service\PubChem;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
        $this->denyAccessUnlessGranted('ROLE_USER');

        $en = $this->getDoctrine()->getManager();

        $pr = $this->getDoctrine()->getRepository(Product::class);
        $prod = $pr->find($product);

        $user = $this->getUser();
        $nameUser = $this->getUser()->getFullName();

        $usage = new Usage();
        $usage->setAction($action);
        $usage->setDate(new DateTime());
        $usage->setProduct($prod);
        $usage->setUser($user);


        $en->persist($usage);

        $en->flush();

        return $this->json(json_encode($nameUser));
    }

    #[Route(path: "new_product", name: 'products.create')]
    public function new(Request $request, NameRepository $nameRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');


        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            dd($form["ncas"]);

            $product = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $actionCreate = (new Usage())
                ->setAction(Usage::ACTION_CREATE)
                ->setUser($this->getUser())
                ->setDate(new DateTime());

            $product->addUsage($actionCreate);

            $entityManager->persist($actionCreate);
            $entityManager->persist($product);


            $name = $nameRepository->findOneBy([
                "name" => $product->getName(),
                "ncas" => $product->getNcas()
            ]);

            if ($name == null) {
                $name = new Name();
                $name->setName($product->getName());
                $name->setNcas($product->getNcas());
                $entityManager->persist($name);
            } else {
                $name->setName($product->getName());
            }


            $entityManager->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté.');

            return $this->redirectToRoute('products.index');
        }

        return $this->render('product_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/json/products/name', name: 'products.search.json')]
    public function jsonSearch(Request $request, NameRepository $nameRepository): Response
    {
        $query = $request->query->get('ncas');
        $res = $nameRepository->findOneBy(['ncas'=>$query]);
        return $this->json($res);
    }

//    #[Route('/json/products/{product_id}/hazards', name: 'products.hazards.json')]
//    public function jsonGetHazards(int $product_id, ProductRepository $productRepository): Response
//    {
//        $product = $productRepository->findOneBy(["id" => $product_id]);
//        if($product !=null) {
//            return $this->json($product->getHazards());
//        }
//
//        return $this->json(null, 404);
//    }

    #[Route('/json/products/compatibilities/check', name: 'products.compatibility_check.json')]
    public function jsonCompatibilityCheck(Request $request, ProductRepository $productRepository, PubChem $pubChem, SerializerInterface $serializer): Response
    {





        $cid = $request->query->get('cid');
        $location = $request->query->get('location');
        if($cid && $location) {
            $p = $pubChem->getHazards($cid);
            $status = $p['status'];
            if($status == 200){
                $hazards = array_map(function ($h) {
                    return $h['code'];
                }, $p['hazards']);

                $incompatibilities = $productRepository->findIncompatibilities($location, $hazards);
                $json = $serializer->serialize($incompatibilities, 'json', ['groups' => ['normal']]);
                return (new JsonResponse())->setContent($json);
                return $this->json($json);

            } else{
                return $this->json(null, $status);
            }

        }


        return $this->json([]);
    }




    #[Route('/history', name: 'history')]
    public function history(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Usage::class);
        $history = $repository->findAll();
        return $this->render('history.html.twig', ['history' => $history,]);
    }




}
