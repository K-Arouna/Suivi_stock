<?php

/**
 * 
 */

// src/Controller/UserController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Form\UserType;

class UserController extends AbstractController
{
   public function list(Request $request): Response
   {
      $this->denyAccessUnlessGranted('ROLE_USER');
      $entityManager = $this->getDoctrine()->getManager();
      $users = $entityManager->getRepository(User::class)->findAll();

      return $this->render('listUsers.html.twig', [
         'users' => $users
      ]);
   }

   public function ajouter(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
   {
      $this->denyAccessUnlessGranted('ROLE_USER');
         $user = new User();

         $formulaire = $this->createForm(UserType::class, $user);

         $formulaire->handleRequest($request);

      if($formulaire->isSubmitted() && $formulaire->isValid()) {
         $boutonClique = $formulaire->getClickedButton();
         
         $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
         $entityManager = $this->getDoctrine()->getManager();
         $entityManager->persist($user);
         $entityManager->flush();

         if($boutonClique->getName() == 'save') {
            return $this->redirectToRoute('ajouter_user');
         }
      }

       return $this->render('nouveauUser.html.twig', [
        'form' => $formulaire->createView()
      ]);
   }

   public function modifier(Request $request, int $user_id): Response
   {
      $this->denyAccessUnlessGranted('ROLE_USER');
      $entityManager = $this->getDoctrine()->getManager();
      $user = $entityManager->getRepository(User::class)->findOneById(intval($user_id));

      if($user) {
         $formulaire = $this->createForm(UserType::class, $user);

         $formulaire->handleRequest($request);

         if($formulaire->isSubmitted() && $formulaire->isValid()) {
         $boutonClique = $formulaire->getClickedButton();
          
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur ajoute avec succes !');

            if($boutonClique->getName() == 'save') {
               return $this->redirectToRoute('list_users');
            }
         }
      }
      return $this->render('nouveauUser.html.twig', [
         'form' => $formulaire->createView()
      ]);
   }
   public function supprimer(Request $request, int $user_id): Response
   {
      $this->denyAccessUnlessGranted('ROLE_USER');
      $entityManager = $this->getDoctrine()->getManager();
      $user = $entityManager->getRepository(User::class)->findOneById(intval($user_id));

      if($user) {
          
         $entityManager->remove($user);
         $entityManager->flush();

      }
      return $this->redirectToRoute('list_users');
    }
}

