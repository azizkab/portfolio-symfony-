<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Skill;
use App\Form\ProjectType;
use App\Service\FileUploader;
use App\Repository\ProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route("/create", name: "create")]
    public function create(Request $request, ManagerRegistry $doctrine, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Upload the file to the server
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $project->setImage($imageFileName);
            }

            $em = $doctrine->getManager();
            $em->persist($project);
            $em->flush();

            // Debug statement
            $this->addFlash("success", "Le projet '{$project->getName()}' a été créée!");
            dump('Project created successfully!');
            return $this->redirectToRoute("read_all");
        }

        return $this->render("project/form.html.twig", [
            "form" => $form->createView(),
            "type" => "create",
        ]);
    }

    #[Route("/readall/{sortBy}", name: "read_all" ,defaults:["sortBy"=>"default"])]
    public function readAll(ManagerRegistry $doctrine, Request $request, ProjectRepository $projectRepository,$sortBy): Response
    {
        $skillRepository = $doctrine->getRepository(Skill::class);

        // Récupérer la liste des compétences
        $skills = $skillRepository->findAll();

        // Récupérer le filtre de compétence depuis la requête
        $selectedSkillId = $request->query->get('skill');

        // Filtrer les projets par compétence si une compétence est sélectionnée
        if ($selectedSkillId) {
            $selectedSkill = $skillRepository->find($selectedSkillId);
            $projects = $projectRepository->findBySkill($selectedSkill);
        } else {
            // Sinon, afficher tous les projets
            $projects = $projectRepository->findAll();
        }

        // Si un mot-clé de recherche est présent, redirige vers la méthode search
        $keyword = $request->query->get('keyword');
        if (!empty($keyword)) {
            return $this->redirectToRoute('search', ['keyword' => $keyword]);
        }

        return $this->render("project/readAll.html.twig", [
            "projects" => $projects,
            "skills" => $skills,
            "sortBy"=>$sortBy,
        ]);
    }


    #[Route("/read/{id}", name: "read")]
    public function read(ManagerRegistry $doctrine, int $id): Response
    {
        $repository = $doctrine->getRepository(Project::class);
        $project = $repository->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        return $this->render('project/read.html.twig', ["project" => $project]);
    }

    #[Route("/update/{id}", name: "update")]
    public function update(Request $request, ManagerRegistry $doctrine, int $id, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $repository = $doctrine->getRepository(Project::class);
        $project = $repository->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Upload the file to the server
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $project->setImage($imageFileName);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            $this->addFlash("Warning", "le projet '{$project->getName()}' a été modifié");

            return $this->redirectToRoute('read', ['id' => $id]);
        }

        return $this->render('project/update.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/delete/{id}", name: "delete")]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $entityManager = $doctrine->getManager();
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }

        $entityManager->remove($project);
        $entityManager->flush();

        return $this->redirectToRoute('read_all');
    }


    #[Route("/search/{sortBy}", name: "search", defaults: ["sortBy"=> "default"])]
    public function search(Request $request, ProjectRepository $projectRepository, $sortBy): Response
    {
        $keyword = $request->query->get('keyword');

        // Si le champ de recherche est vide, redirige vers la page read_all
        if (empty($keyword)) {
            return $this->redirectToRoute('read_all', ['sortBy' => $sortBy]);
        }

        // Utilise la méthode de recherche dans le repository pour obtenir les résultats
        $projects = $projectRepository->findByKeyword($keyword, $sortBy);

        return $this->render("project/search.html.twig", [
            "projects" => $projects,
            "searchKeyword" => $keyword,
            "sortBy" => $sortBy,
        ]);
    }

}
