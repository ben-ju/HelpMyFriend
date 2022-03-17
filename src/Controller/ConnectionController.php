<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ConnectionType;
use Doctrine\Persistence\ManagerRegistry;

class ConnectionController extends AbstractController
{

    #[Route('/connection', name: 'app_connection')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {


        $connectionForm = $this->createForm(ConnectionType::class)->handleRequest($request);

        if ($connectionForm->isSubmitted() && $connectionForm->isValid()) {
            $connection = $connectionForm->getData();
            $this->checkUtilisateur($doctrine, $connection);
        }



        return $this->render('connection/index.html.twig', [
            'controller_name' => 'ConnectionController',
            'monFormulaire' => $connectionForm->createView(),
        ]);
    }


    public function checkUtilisateur(ManagerRegistry $doctrine, array $infos)
    {
        $identification = $doctrine->getRepository(Utilisateur::class)->findBy($infos);

        if ($identification == []) {
            $this->addFlash("danger", "Votre mot de passe ou votre e-mail est incorrect");
        } else {
        }
    }
}
