<?php
namespace App\Controller;

use App\Entity\Appart;
use App\Form\Admin\Filter\FilterAppartType;
use App\Form\Admin\ListingType;
use App\Form\Appart1Type;
use App\Form\AppartType;
use App\Repository\AppartRepository;
use http\Exception;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class AppartTestCrud extends AbstractController
{
    private string $templatesBase = 'appart/crud/';

    /**
     * @Route("/base",name= "base")
     */
    public function getbase()
    {
        return $this->container;
    }




    /** @Route("/project/all")
     */
    public function showProject(Request $request, $listingController,AppartRepository $appartRepository): Response
    {
        $options = [];
        $form = $this->createForm(ListingType::class, null, $options);
        $form->handleRequest($request);

        $filtersForm = FilterAppartType::class;
        $filtersFormParams = [];

        $decorate = AppartListingController::QUERY_ALL;
        $decorateParams = [];

        $listingController->setDefaultOrder("createdDate", 'DESC');

        $items = $listingController->getItemsFromRequest($request, $filtersForm, $filtersFormParams, $decorate, $decorateParams);
        return $this->render($this->templatesBase . 'index.html.twig', $items);
    }

    /** @Route("/delete/{id}",name = "project_delete")
     */
    public function deleteProject(Appart $appart): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        if (!$project) {
            $this->addFlash("danger", "Le projet n'existe pas");
            return $this->redirect('/project/all');
        }

        $entityManager->remove($project);
        $entityManager->flush();

        $this->addFlash("danger", "Le projet a été supprimé");
        return $this->redirect('/project/all');
    }

    /**
     * @Route ("/edit/{id}", name = "project_edit")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request, Appart $appart, AppartRepository $appartRepository): Response
    {
        $form = $this->createForm(Appart1Type::class, $appart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appartRepository->add($appart);
            return $this->redirectToRoute('app_appart_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('appart_crud/edit.html.twig', [
            'appart' => $appart,
            'form' => $form,
        ]);
    }
    private function createProject(Project $project){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($project);
        $entityManager->flush();
    }

    /**
     * @Route("/project/update", name="project_update")
     */
    public function update(RedmineCommunicator $ticketMethods, ProjectRepository $projectRepository): Response
    {
        $project_redmine = $ticketMethods->getProjects()["projects"];
        foreach ($project_redmine as $i) {

            if (!($projectRepository->findOneBy(["id_redmine" => $i["id"]]))) {
                $project = new Project();
                $project->setName($i["name"]);
                $project->setIdRedmine($i["id"]);
                $project->setState($i["status"]);
                $this->createProject($project);
            }
        }
        $this->addFlash("success", "La BDD a été actualisé");
        return $this->redirect('/project/all');
    }

    /**
     * @Route("/project/new", name="project_create")
     */
    public function CreateFormProject(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectForm::class,$project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $this->createProject($project);

            $this->addFlash("success", "Le projet a été ajouté");
            return $this->redirect('/project/all');
        }
        return $this->render('form/create.html.twig', [
            'monFormulaire' => $form->createView()
        ]);
    }

    /** @Route("/script/{id}", name="project_script")
     * @param $id
     * @return Response
     */
    public function scriptProject($id,RouterInterface $router): Response
    {
        $projectRepository = $this->getDoctrine()->getRepository(Project::class);
        $project = $projectRepository->find($id);
        if ($project == null) {
            return new Response('console.error("projet introuvable")', 200, ["Content-Type" => "text/javascript"]);
        } else {
            if ($project->getState() == 0) {
                return new Response('console.error("projet désactivé")', 200, ["Content-Type" => "text/javascript"]);
            } elseif ($project->getState() == 1) {

                $context = $router->getContext();
                $hostname = $context->getScheme() ."://". $context->getHost() . $context->getBaseUrl()."/";

                $data = ["introduction" => $project->getDescription(),"url" => $hostname,"id_redmine" => $project->getIdRedmine()];

                $introduction = "window.tagticket=".json_encode($data).";";
                $file = file_get_contents("../public/build/js/integrator.js");
                return new Response($introduction.$file, 200, ["Content-Type" => "text/javascript"]);
            }
            else{
                return new Response('console.error("Etat du projet incorrect")', 200, ["Content-Type" => "text/javascript"]);
            }
        }
    }
}