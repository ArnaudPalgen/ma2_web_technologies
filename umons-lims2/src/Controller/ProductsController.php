<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Name;
use App\Entity\Product;
use App\Entity\Usage;
use App\Form\ProductType;
use App\Repository\HazardRepository;
use App\Repository\LocationRepository;
use App\Repository\NameRepository;
use App\Repository\ProductRepository;
use App\Service\PubChem;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
    public function new(Request $request, NameRepository $nameRepository,  HazardRepository $hazardRepository, ProductRepository $productRepository, PubChem $pubChem): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');


        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $p = $form->getData();


            $h = $pubChem->getHazards($form['cid']->getData());
            $status = $h['status'];


            if($status == 200){
                $hazards = array_map(function ($h) {
                    return $h['code'];
                }, $h['hazards']);


                $hazardEntities = $hazardRepository->findBy(["id" => $hazards]);

                if($form['isIgnoreConflict']->getData() != 'true'){
                    $incompatibilities = $productRepository->findIncompatibilities($p->getLocation()->getId(), $hazards);
                    if($incompatibilities) {
                        $this->addFlash("danger", "Erreur: des incompatibilités ont été trouvés");

                        return $this->render('product_create.html.twig', [
                            'form' => $form->createView(),
                        ]);
                    }
                }

                for($i = 0; $i < $form["count"]->getData(); $i++) {
                    $product = (new Product())
                        ->setNcas($p->getNcas())
                        ->setLocation($p->getLocation())
                        ->setName($p->getName())
                        ->setSize($p->getSize())
                        ->setConcentration($p->getConcentration())
                    ;

                    foreach ($hazardEntities as $hazardEntity) {
                        $product->addHazard($hazardEntity);
                    }


                    $actionCreate = (new Usage())
                        ->setAction(Usage::ACTION_CREATE)
                        ->setUser($this->getUser())
                        ->setDate(new DateTime());

                    $product->addUsage($actionCreate);

                    $entityManager->persist($actionCreate);
                    $entityManager->persist($product);


                }
                $name = $nameRepository->findOneBy([
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


            }else{
                $this->addFlash('danger', "A problem occurred while retrieving data from pubchem");

            }


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



    #[Route('/json/products/compatibilities/check', name: 'products.compatibility_check.json')]
    public function jsonCompatibilityCheck(Request $request, ProductRepository $productRepository, PubChem $pubChem): Response
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

//                dump($hazards);

                $incompatibilities = $productRepository->findIncompatibilities($location, $hazards);

                return $this->json($incompatibilities);

            } else{
                return $this->json(null, $status);
            }

        }


        return $this->json([]);
    }

    // todo: handle if cas is not known



    #[Route('/history', name: 'history')]
    public function history(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Usage::class);
        $history = $repository->findAll();
        return $this->render('history.html.twig', ['history' => $history,]);
    }

    #[Route('/products/move', name: 'products.move')]
    public function moveProduct(Request $request, PubChem $pubChem , LocationRepository $locationRepository, HazardRepository $hazardRepository, ProductRepository $productRepository) {

        $product_ids =  $request->query->get('products');

        if(!$product_ids) {
            return $this->redirectToRoute('products.index');
        }

        $products = $productRepository->findBy(["id" => $product_ids]);

        $existing_product_ids = array_map(function ($p) {
            return [
                'id' => $p->getId(),
                'ncas' => $p->getNcas()
            ];
        }, $products);



        $form = $this->createFormBuilder()

            ->add('location', EntityType::class, [
                'label' => 'Nouvel Emplacement',
                'class' => Location::class,
                'choice_label' => function ($category) {
                    return $category->getDisplayName();
                }
            ])
            ->add('isIgnoreConflict', HiddenType::class, [
                'empty_data' => false,
                'data' => false,
            ])
            ->add('products', HiddenType::class, [
                'data' => json_encode($existing_product_ids)
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $prdts = json_decode($data[]);


            foreach ($prdts as $prd) {

                $pEntity = $productRepository->find($prd['id']);


                $h = $pubChem->getHazards($data['cid']);
                $status = $h['status'];


                if($status == 200){
                    $hazards = array_map(function ($h) {
                        return $h['code'];
                    }, $h['hazards']);


                    if($data['isIgnoreConflict'] != 'true'){
                        $incompatibilities = $productRepository->findIncompatibilities($data['location'], $hazards);
                        return $this->json([
                            'success' => false,
                            'incompatibilities' => $incompatibilities
                        ]);
                    }

                    $pEntity->setLocation($data['location']);

                    $this->getDoctrine()->getManager()->flush();
                    return $this->json([
                        'success' => true,
                    ]);


                }
            }




        }

        return $this->render('product_move.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);



    }



}
