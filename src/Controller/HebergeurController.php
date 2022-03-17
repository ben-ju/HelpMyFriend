<?php

namespace App\Controller;

use App\Entity\Hebergeur;
use App\Form\HebergeurForm;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HebergeurController extends AbstractController
{
    /*  #[Route('/hebergeur', name: 'app_hebergeur')]
    public function index(): Response
    {
        return $this->render('hebergeur/index.html.twig', [
            'controller_name' => 'HebergeurController',
        ]);
    }*/


    private function createHebergeur(Hebergeur $appart, ManagerRegistry $doctrine)
    {

        $em = $doctrine->getManager();
        $em->persist($appart);
        $em->flush();
    }


    /**
     * @Route("/hebergeur")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {

        $hebergeur = new Hebergeur();

        $hebergeurForm = $this->createForm(HebergeurForm::class);
        $hebergeurForm->handleRequest($request);

        if ($hebergeurForm->isSubmitted() && $hebergeurForm->isValid()) {
            $hebergeur = $hebergeurForm->getData();
            $this->createHebergeur($hebergeur, $doctrine);
            $this->addFlash("success", "Inscription hébergeur effectuée");
        }

        return $this->render('hebergeur/index.html.twig', [
            'monFormulaire' => $hebergeurForm->createView(),
        ]);
    }
}
