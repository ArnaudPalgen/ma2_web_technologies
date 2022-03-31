<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Name;
use App\Entity\Product;
use App\Entity\Usage;
use App\Form\ProductFormType;
use App\Repository\HazardRepository;
use App\Repository\NameRepository;
use App\Repository\ProductRepository;
use App\Service\PubChem;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/history', name: 'history')]
    public function history(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Usage::class);
        $history = $repository->findAll();
        return $this->render('history.html.twig', ['history' => $history,]);
    }


    #[Route(path: "/products/new", name: 'products.new')]
    public function newProduct(Request $request, ProductRepository $pr, HazardRepository $hr, NameRepository $nr, PubChem $pubChem)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $f_product = new Product();

        $form = $this->createForm(ProductFormType::class, $f_product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hazards = $pubChem->getHazardsByNcas($f_product->getNcas());
			if(is_null(null)){
				$hazards = array();
			}	

            $hazards = array_map(function ($h) {
                return $h['code'];
            }, $hazards);


            if (! is_null($hazards)) {
                if (count($hazards) > 0
                    && $form->get('is_ignore_conflicts')->getData() != 'true'
                    && $incompatibilities = $pr->findIncompatibilities($f_product->getLocation(), $hazards)
                ) {

                    return $this->render('product_create.html.twig', [
                        'form' => $form->createView(),
                        'incompatibilities' => $incompatibilities
                    ]);
                }
            } else {
                $this->addFlash("danger", "Une erreur s'est produite durant la communication avec PubChem.");
                return $this->render('product_create.html.twig', [
                    'form' => $form->createView(),
                    'incompatibilities' => null
                ]);
            }


            $em = $this->getDoctrine()->getManager();

            for ($i = 0; $i < $form->get("count")->getData(); $i++) {
                $product = (new Product())
                    ->setNcas($f_product->getNcas())
                    ->setLocation($f_product->getLocation())
                    ->setName($f_product->getName())
                    ->setSize($f_product->getSize())
                    ->setConcentration($f_product->getConcentration());

                foreach ($hr->findBy(["id" => $hazards]) as $hazardEntity) {
                    $product->addHazard($hazardEntity);
                }


                $actionCreate = (new Usage())
                    ->setAction(Usage::ACTION_CREATE)
                    ->setUser($this->getUser())
                    ->setDate(new DateTime());

                $product->addUsage($actionCreate);

                $em->persist($actionCreate);
                $em->persist($product);


            }
            $name = $nr->findOneBy([
                "ncas" => $f_product->getNcas()
            ]);

            if ($name == null) {
                $name = new Name();
                $name->setName($f_product->getName());
                $name->setNcas($f_product->getNcas());
                $em->persist($name);
            } else {
                $name->setName($f_product->getName());
            }

            $em->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté.');

            return $this->redirectToRoute('products.index');
        }


        return $this->render('product_create.html.twig', [
            'form' => $form->createView(),
            'incompatibilities' => null
        ]);
    }


    #[Route('/products/move', name: 'products.move')]
    public function moveProduct(Request $request, ProductRepository $pr)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $product_ids = $request->query->get('products', []);

        if (empty($product_ids)) {
            return $this->redirectToRoute('products.index');
        }

        $products = $pr->findBy(["id" => $product_ids]);

        $form = $this->createFormBuilder()
            ->add('location', EntityType::class, [
                'label' => 'Nouvel Emplacement',
                'class' => Location::class,
                'choice_label' => function ($category) {
                    return $category->getDisplayName();
                }
            ])
            ->add('is_ignore_conflicts', HiddenType::class, [
                'empty_data' => false,
                'data' => false,
            ])->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();


            $incompatibilities = [];
            if ($form->get('is_ignore_conflicts')->getData() != 'true') {
                foreach ($products as $product) {
                    $p_hazard = $product->getHazards();
                    if (count($p_hazard) > 0 && $p_incompatibilities = $pr->findIncompatibilities($data['location'], $p_hazard)) {
                        $incompatibilities[$product->getNcas()] = $p_incompatibilities;
                    }
                }

                if (!empty($incompatibilities)) {
                    return $this->render('product_move.html.twig', [
                        'form' => $form->createView(),
                        'products' => $products,
                        'incompatibilities' => $incompatibilities
                    ]);
                }
            }

            $em = $this->getDoctrine()->getManager();
            foreach ($products as $product) {
                $product->setLocation($data['location']);

                $actionCreate = (new Usage())
                    ->setAction(Usage::ACTION_MOVE)
                    ->setUser($this->getUser())
                    ->setDate(new DateTime());

                $em->persist($actionCreate);
                $product->addUsage($actionCreate);

                $em->flush();
            }

            $this->addFlash('success', 'Les produits ont bien été ajoutés.');

            return $this->redirectToRoute('products.index');

        }

        return $this->render('product_move.html.twig', [
            'form' => $form->createView(),
            'products' => $products,
            'incompatibilities' => null
        ]);
    }

    #[Route('/products/autocomplete', name: 'products.autocomplete')]
    public function autocompleteProduct(NameRepository $nr)
    {

        return $this->json($nr->findAllIndexedByCAS());
    }


}
