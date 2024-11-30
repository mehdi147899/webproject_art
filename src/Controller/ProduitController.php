<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'produit_index')]
    public function index(ProduitRepository $produitRepository): Response
    {
        // Fetch all products from the database
        $produits = $produitRepository->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    
    #[Route('/produit/new', name: 'produit_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $produit = new Produit();
    
        // Create the form
        $form = $this->createForm(ProduitType::class, $produit);
    
        // Handle the form submission
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // If form is valid, persist the new product to the database
            $em->persist($produit);
            $em->flush();
    
            // Add a flash message for success
            $this->addFlash('success', 'Product has been added successfully!');
    
            // Redirect to the gallery page or wherever you want after saving the product
            return $this->redirectToRoute('app_Galerie');
        }
    
        // In case the form is not submitted or not valid, render the form again
        return $this->render('produit/New.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/produit/{id}/edit', name: 'produit_edit')]
    public function edit(Produit $produit, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Update the product in the database
            $em->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/EditProduit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/produit/{id}/delete', name: 'produit_delete')]
    public function delete(Produit $produit, EntityManagerInterface $em): Response
    {
        // Remove the product from the database
        $em->remove($produit);
        $em->flush();

        return $this->redirectToRoute('produit_index');
    }
   
    
}
