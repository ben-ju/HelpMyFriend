<?php

namespace App\Controller;

use App\Entity\Hebergeur;
use App\Form\HebergeurForm;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class HebergeurController extends AbstractController
{
    /*  #[Route('/hebergeur', name: 'app_hebergeur')]
    public function index(): Response
    {
        return $this->render('hebergeur/index.html.twig', [
            'controller_name' => 'HebergeurController',
        ]);
    }*/


    private function createHebergeur(Hebergeur $hebergeur, ManagerRegistry $doctrine)
    {

        $em = $doctrine->getManager();
        $em->persist($hebergeur);
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

            /*  $plaintextPassword = $request->request->get('password');

            // hash the password (based on the security.yaml config for the $user class)
            $hashedPassword = $passwordHasher->hashPassword(
                $hebergeur,
                $plaintextPassword
            );
            $hebergeur->setPassword($hashedPassword);
*/

            /* $hashedPassword = password_hash($request->request->get('password'), PASSWORD_DEFAULT);
            $hebergeur->setPassword($hashedPassword);*/

            $hebergeur = $hebergeurForm->getData();
            $this->createHebergeur($hebergeur, $doctrine);
            $this->addFlash("success", "Inscription hébergeur effectuée");
        }

        return $this->render('hebergeur/index.html.twig', [
            'monFormulaire' => $hebergeurForm->createView(),
        ]);
    }
}
