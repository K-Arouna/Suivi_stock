<?php

/**
 * 
 */

 // src/Controller/ProduitController.php
 namespace App\Controller;

 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\Response;

 use App\Entity\Produit;
 use App\Form\ProduitType;

 class ProduitController extends AbstractController
 {  
    public function list(Request $request): Response
    {
      $this->denyAccessUnlessGranted('ROLE_USER');
      $entityManager = $this->getDoctrine()->getManager();
      $produits = $entityManager->getRepository(Produit::class)->findAll();

      return $this->render('listProduits.html.twig', [
         'produits' => $produits
      ]);
    }

    public function ajouter(Request $request): Response
    {
      $this->denyAccessUnlessGranted('ROLE_USER');
       $produit = new Produit();

       $formulaire = $this->createForm(ProduitType::class, $produit);

       $formulaire->handleRequest($request);

       if($formulaire->isSubmitted() && $formulaire->isValid()) {
          $boutonClique = $formulaire->getClickedButton();
          
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($produit);
          $entityManager->flush();

          $this->addFlash('success', 'Produit ajoute avec succes !');

          if($boutonClique->getName() == 'save') {
             return $this->redirectToRoute('ajouter_produit');
          }
       }

       return $this->render('nouveauProduit.html.twig', [
          'form' => $formulaire->createView()
       ]);
    }

    public function modifier(Request $request, int $produit_id): Response
    {
      $this->denyAccessUnlessGranted('ROLE_USER');
      $entityManager = $this->getDoctrine()->getManager();
      $produit = $entityManager->getRepository(Produit::class)->findOneById(intval($produit_id));

      if($produit) {
         $formulaire = $this->createForm(ProduitType::class, $produit);

         $formulaire->handleRequest($request);

         if($formulaire->isSubmitted() && $formulaire->isValid()) {
          $boutonClique = $formulaire->getClickedButton();
          
            $entityManager->persist($produit);
            $entityManager->flush();

            if($boutonClique->getName() == 'save') {
               return $this->redirectToRoute('list_produits');
            }
         }
      }
       return $this->render('nouveauProduit.html.twig', [
         'form' => $formulaire->createView()
       ]);
    }
    public function supprimer(Request $request, int $produit_id): Response
    {
      $this->denyAccessUnlessGranted('ROLE_USER');
      $entityManager = $this->getDoctrine()->getManager();
      $produit = $entityManager->getRepository(Produit::class)->findOneById(intval($produit_id));

      if($produit) {
          
         $entityManager->remove($produit);
         $entityManager->flush();

      }
      return $this->redirectToRoute('list_produits');
   }
 }
