<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class UserController extends AbstractController
{
    #[Route('/createuser', name: 'createuser')]
    public function create(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine, UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator): Response
    {
        $user = new User($passwordHasher);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // return $this->redirectToRoute('login');
            $userAuthenticator->authenticateUser($user, $formLoginAuthenticator, $request);
            return $this->redirectToRoute('read_all');
        }
        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}