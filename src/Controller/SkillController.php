<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Form\SkillType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SkillController extends AbstractController
{
    #[Route("/create_skill", name: "create_skill")]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $skill = new Skill();
        $form = $this->createForm(SkillType::class, $skill);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($skill);
            $em->flush();
            $this->addFlash("success", "La compétence '{$skill->getName()}' a été créée !");
            return $this->redirectToRoute("read_all");
        }

        return $this->render("skill/form.html.twig", [
            "form" => $form->createView(),
            "type" => "create",
        ]);
    }

    # SkillController.php

    public function readAll(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Skill::class);
        $skills = $repository->findAll();

        // Fetch associated projects for each skill
        foreach ($skills as $skill) {
            $skill->getProjects(); // Assuming there's a method named getProjects in your Skill entity
        }

        return $this->render("project/readAll.html.twig", ["skills" => $skills]);
    }



    #[Route("/read_skill/{id}", name: "read_skill")]
    public function read(ManagerRegistry $doctrine, int $id): Response
    {
        $repository = $doctrine->getRepository(Skill::class);
        $skill = $repository->find($id);

        return $this->render('skill/read.html.twig', ["skill" => $skill]);
    }

    #[Route("/update_skill/{id}", name: "update_skill")]
    public function updateSkill(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

        $repository = $doctrine->getRepository(Skill::class);
        $skill = $repository->find($id);

        if (!$skill) {
            throw $this->createNotFoundException('Compétence non trouvée');
        }

        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('read_skill', ['id' => $id]);
        }

        return $this->render('skill/update.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/delete_skill/{id}", name: "delete_skill")]
    public function deleteSkill(ManagerRegistry $doctrine, int $id): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

        $entityManager = $doctrine->getManager();
        $skill = $entityManager->getRepository(Skill::class)->find($id);

        if (!$skill) {
            throw $this->createNotFoundException('Compétence non trouvée');
        }

        $entityManager->remove($skill);
        $entityManager->flush();

        return $this->redirectToRoute('read_all');
    }
}
