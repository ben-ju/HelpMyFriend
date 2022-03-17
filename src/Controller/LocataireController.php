<?php

namespace App\Controller;

use App\Entity\Locataire;
use App\Form\LocataireForm;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocataireController extends AbstractController
{
    private function createLocataire(Locataire $appart, ManagerRegistry $doctrine)
    {

        $em = $doctrine->getManager();
        $em->persist($appart);
        $em->flush();
    }


    /**
     * @Route("/locataire")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {

        $locataire = new Locataire();

        $locataireForm = $this->createForm(LocataireForm::class);
        $locataireForm->handleRequest($request);

        if ($locataireForm->isSubmitted() && $locataireForm->isValid()) {
            $locataire = $locataireForm->getData();
            $this->createLocataire($locataire, $doctrine);
            $this->addFlash("success", "Inscription du locataire effectuée");
        }

        return $this->render('locataire/index.html.twig', [
            'monFormulaire' => $locataireForm->createView(),
        ]);
    }
}
