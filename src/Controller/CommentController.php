<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/comments", name: "comment_index")]
    public function index(): Response
    {
        $comments = $this->entityManager->getRepository(Comment::class)->findAll();

        return $this->render('comment/index.html.twig', ['comments' => $comments]);
    }

    #[Route("/comment/create", name: "comment_create")]
    public function create(Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/comment/{id}/like", name: "comment_like", methods: ["POST"])]
    public function like(Comment $comment): JsonResponse
    {
        $comment->setLikes($comment->getLikes() + 1);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->json([
            'likes' => $comment->getLikes(),
            'dislikes' => $comment->getDislikes(),
        ]);
    }

    #[Route("/comment/{id}/dislike", name: "comment_dislike", methods: ["POST"])]
    public function dislike(Comment $comment): JsonResponse
    {
        $comment->setDislikes($comment->getDislikes() + 1);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->json([
            'likes' => $comment->getLikes(),
            'dislikes' => $comment->getDislikes(),
        ]);
    }
}
