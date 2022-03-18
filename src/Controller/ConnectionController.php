<?php

namespace App\Controller;

use App\Entity\Hebergeur;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ConnectionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class ConnectionController extends AbstractController
{

    #[Route('/connection', name: 'app_connection')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {


        $current_session = $request->getSession();
        if ($current_session->get('utilisateur') != null) {
            return $this->redirect('/appart/crud');
        }

        $connectionForm = $this->createForm(ConnectionType::class)->handleRequest($request);

        if ($connectionForm->isSubmitted() && $connectionForm->isValid()) {
            $connection = $connectionForm->getData();
            $this->checkUtilisateur($doctrine, $connection);
        }


        if ($current_session->get('utilisateur') != null) {
            return $this->redirect('/appart/crud');
        } else {
            return $this->render('connection/index.html.twig', [
                'controller_name' => 'ConnectionController',
                'monFormulaire' => $connectionForm->createView(),
                'session_role' => $request->getSession()
            ]);
        }
    }


    public function checkUtilisateur(ManagerRegistry $doctrine, array $infos)
    {
        $identification = $doctrine->getRepository(Utilisateur::class)->findBy($infos);
        //   $roleUtilisateur = $doctrine->getRepository(Utilisateur::class)->findBy($identification);
        if ($identification == []) {
            $this->addFlash("danger", "Votre mot de passe ou votre e-mail est incorrect");
        } else {
            $idUtilisateur = $identification[0]->getId();

            $roleUtilisateur = $doctrine->getRepository(Hebergeur::class)->findBy(['id_utilisateur_fk' => $idUtilisateur]);
            if ($roleUtilisateur != []) {
                $role = 'hebergeur';
            } else {
                $role = 'locataire';
            }

            $session = new Session(new PhpBridgeSessionStorage());
            $session->start();
            $session->set('utilisateur', $infos['email']);
            $session->set('id_utilisateur', $idUtilisateur);
            $session->set('role', $role);
        }
    }
}
