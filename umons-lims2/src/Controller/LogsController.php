<?php
// src/Controller/LogsController.php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\Usage;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LogsController extends AbstractController{

     /**
     * @Route("/logs")
     */
    public function logs(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Usage::class);
        $logs = $repository->findAll();
        return $this->render('logs.html.twig', ['logs' => $logs, ]);
    }
}