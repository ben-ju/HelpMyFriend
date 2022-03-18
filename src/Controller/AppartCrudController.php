<?php

namespace App\Controller;

use App\Entity\Appart;
use App\Entity\Groupe;
use App\Form\Admin\Filter\FilterAppartType;
use App\Form\Admin\ListingType;
use App\Form\Appart1Type;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\AppartRepository;
use App\Repository\GroupeRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/appart/crud')]
class AppartCrudController extends AbstractController
{

    private $templatesBase = 'appart_crud/';


    #[Route('/', name: 'app_appart_crud_index', methods: ['GET'])]
    public function index(AppartRepository $appartRepository, Request $request, AppartListingController $listingController, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(ListingType::class, null, $options=[]);
        $form->handleRequest($request);

        $filtersForm = FilterAppartType::class;
        $filtersFormParams = [];

        $decorate = AppartListingController::QUERY_ALL;
        $decorateParams = [];

        $listingController->setDefaultOrder("id", 'DESC');

        $items = $listingController->getItemsFromRequest($request, $doctrine, $filtersForm, $filtersFormParams, $decorate, $decorateParams);
        return $this->render($this->templatesBase . 'index.html.twig', $items);
    }

    #[Route('/new', name: 'app_appart_crud_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        AppartRepository $appartRepository
    ): Response {
        $appart = new Appart();
        $form = $this->createForm(Appart1Type::class, $appart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appartRepository->add($appart);
            return $this->redirectToRoute(
                'app_appart_crud_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('appart_crud/new.html.twig', [
            'appart' => $appart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appart_crud_show', methods: ['GET', 'POST'])]
    public function show(
        Appart $appart,
        Request $request,
        GroupeRepository $groupeRepository,
        ManagerRegistry $managerRegistry
    ): Response {
        $reservation = new Reservation();

        // TODO must refactor to separate code on a different controller, use event listener

        $reservationForm = $this->createForm(
            ReservationType::class,
            $reservation
        );

        $reservationForm->handleRequest($request);

        if ($reservationForm->isSubmitted() && $reservationForm->isValid()) {
            $em = $managerRegistry->getManager();

            $reservation = $reservationForm->getData();
            $data = $request->request->all()['reservation'];

            $startDate = $data['date_debut'];
            $inputStart = "{$startDate['day']}/{$startDate['month']}/{$startDate['year']}";

            $endDate = $data['date_fin'];
            $inputEnd = "{$endDate['day']}/{$endDate['month']}/{$endDate['year']}";

            $reservation = new Reservation();
            $reservation->setDateDebut(new DateTime($inputStart));
            $reservation->setDateFin(new DateTime($inputEnd));
            $reservation->setIdAppartFk($appart);

            // no implementation of group / users
            $allGroups = $groupeRepository->findAll();
            if ($allGroups === [] || $allGroups === false) {
                $groupe = new Groupe();
                $groupe->setNom('Groupe');
                $em->persist($groupe);
                $reservation->setIdGroupeFk($groupe);
            } else {
                $reservation->setIdGroupeFk($allGroups[0]);
            }
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute(
                'app_appart_crud_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('appart_crud/show.html.twig', [
            'appart' => $appart,
            'reservationForm' => $reservationForm->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_appart_crud_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Appart $appart,
        AppartRepository $appartRepository
    ): Response {
        $form = $this->createForm(Appart1Type::class, $appart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appartRepository->add($appart);
            return $this->redirectToRoute(
                'app_appart_crud_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('appart_crud/edit.html.twig', [
            'appart' => $appart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appart_crud_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Appart $appart,
        AppartRepository $appartRepository
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $appart->getId(),
                $request->request->get('_token')
            )
        ) {
            $appartRepository->remove($appart);
        }

        return $this->redirectToRoute(
            'app_appart_crud_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}


