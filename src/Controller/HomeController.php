<?php

namespace App\Controller;

use App\Entity\Hebergeur;
use App\Form\HebergeurForm;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route("/", name: "homepage")]
    public function index(): Response
    {
        return $this->render('homepage.html.twig');
    }
}
