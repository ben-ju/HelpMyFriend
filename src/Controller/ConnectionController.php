<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ConnectionType;

class ConnectionController extends AbstractController
{
    #[Route('/connection', name: 'app_connection')]
    public function index(Request $request): Response
    {

        $connectionForm = $this->createForm(ConnectionType::class)->handleRequest($request);

        return $this->render('connection/index.html.twig', [
            'controller_name' => 'ConnectionController',
            'monFormulaire' => $connectionForm->createView(),
        ]);
    }
}
