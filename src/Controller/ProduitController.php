<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProduitController extends AbstractController
{

    // src/Controller/ProduitController.php

    private string $uploadsDirectory;

    public function __construct(string $uploadsDirectory)
    {
        $this->uploadsDirectory = $uploadsDirectory;
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
            // Handle image upload
            $imageFile = $form->get('image')->getData();
    
            if ($imageFile) {
                // Generate a unique name for the file
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
    
                // Move the file to the uploads directory
                $imageFile->move($this->uploadsDirectory, $newFilename);
    
                // Set the image name in the product entity
                $produit->setImage($newFilename);
            }
    
            // Persist the new product to the database
            $em->persist($produit);
            $em->flush();
    
            // Add a flash message for success
            $this->addFlash('success', 'Produit ajouté avec succès !');
    
            // Redirect to the previous page
            $referer = $request->headers->get('referer');
            if ($referer) {
                return $this->redirect($referer);
            }
    
            // Fallback if no referer is available
            return $this->redirectToRoute('app_galerie');
        }
    
        // Render the form view
        return $this->render('produit/New.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produits', name: 'app_produit_all')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();

        return $this->render('produit/AllProduit.html.twig', [
            'produits' => $produits,
        ]);
    }

    // Route for editing a product
    #[Route('/produit/edit/{id}', name: 'app_produit_edit')]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Define the upload directory
                $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';

                // Generate a unique file name
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Move the uploaded file to the target directory
                $imageFile->move($uploadDirectory, $newFilename);

                // Set the new file name to the product
                $produit->setImage($newFilename);
            }

            // Save the product using the EntityManager
            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès.');

            return $this->redirectToRoute('app_produit_all');
        }

        return $this->render('produit/EditProduit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }
    #[Route('/produit/{id}/delete', name: 'app_produit_delete')]
    public function delete(Produit $produit, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager, Request $request): RedirectResponse
    {
        // CSRF validation logic
        $csrfToken = $request->request->get('_csrf_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete' . $produit->getId(), $csrfToken))) {
            throw new \Exception('Token CSRF invalide.');
        }

        // Proceed with deleting the product
        $em->remove($produit);
        $em->flush();

        $this->addFlash('success', 'Produit supprimé avec succès.');

        return $this->redirectToRoute('app_produit_all'); // Assuming 'app_produit_all' is the route for listing products
    }

    #[Route('/profile/{id}', name: 'app_profile')]
    public function profile(Request $request, int $id, Security $security, SluggerInterface $slugger): Response
    {
        // Get the currently logged-in user
        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to access this page.');
        }



        // Handle the form submission (image update)
        if ($request->isMethod('POST') && $request->files->get('image')) {
            $image = $request->files->get('image');

            if ($image) {
                // Generate a unique file name based on the original file name
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $image->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading image');
                    return $this->redirectToRoute('app_profile', ['id' => $id]);
                }

                // Update the user's image in the database
                $entityManager = $this->$this->getDoctrine()->getManager();
                $user->$user->setImage($newFilename);
                $entityManager->flush();

                $this->addFlash('success', 'Profile image updated successfully.');
            }
        }

        // Pass the user data to the Twig template
        return $this->render('security/Profile.html.twig', [
            'user' => $user,
        ]);
    }



}
