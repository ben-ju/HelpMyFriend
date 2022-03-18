<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends AbstractController
{
    #[Route('/logout', name: 'app_logout')]
    public function index(Request $request): Response
    {
        $current_session = $request->getSession();
        $current_session->invalidate();
        $this->addFlash("Warning", "Déconnexion réussie");

        // return $this->redirectToRoute('connection');
        return $this->render('logout/index.html.twig', [
            'controller_name' => 'LogoutController',
        ]);
    }
}
