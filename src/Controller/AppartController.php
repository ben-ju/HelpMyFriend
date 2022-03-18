<?php

namespace App\Controller;

use App\Entity\Appart;
use App\Form\AppartType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;



class AppartController extends AbstractController
{

    private function createAppart(Appart $appart, ManagerRegistry $doctrine)
    {

        $em = $doctrine->getManager();
        $em->persist($appart);
        $em->flush();
    }


    /**
     * @Route("/appart")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {

        $appart = new Appart();
        $idUtilisateur = $request->getSession()->get('id_utilisateur');

        $appartForm = $this->createForm(AppartType::class);
        $appartForm->handleRequest($request);

        if ($appartForm->isSubmitted() && $appartForm->isValid()) {
            $appart = $appartForm->getData();

            $this->addFlash("success", "La location est prête");

            $this->createAppart($appart, $doctrine);
        }

        return $this->render('appart/index.html.twig', [
            'monFormulaire' => $appartForm->createView(),
        ]);
    }
}
