<?php


namespace App\Controller;

use App\Entity\Artiste;
use App\Form\ArtisteType;
use App\Repository\ArtisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/artiste')]
class ArtisteController extends AbstractController
{
    #[Route('/', name: 'artiste_index', methods: ['GET'])]
    public function index(ArtisteRepository $artisteRepository): Response
    {
        return $this->render('artiste/index.html.twig', [
            'artistes' => $artisteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'artiste_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ArtisteRepository $artisteRepository): Response
    {
        // Check if there is already an artist
        if ($artisteRepository->count([]) > 0) {
            $this->addFlash('warning', 'An artist already exists. You cannot add more.');
            return $this->redirectToRoute('artiste_index');
        }

        $artiste = new Artiste();
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
                $artiste->setImage($newFilename);
            }

            $entityManager->persist($artiste);
            $entityManager->flush();

            $this->addFlash('success', 'Artiste created successfully!');
            return $this->redirectToRoute('artiste_index');
        }

        return $this->render('artiste/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'artiste_show', methods: ['GET'])]
    public function show(Artiste $artiste): Response
    {
        return $this->render('artiste/show.html.twig', [
            'artiste' => $artiste,
        ]);
    }

    #[Route('/{id}/edit', name: 'artiste_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Artiste $artiste, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload manually
            $file = $form->get('image')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // Generate a unique name for the file
                $newFilename = uniqid() . '-' . $originalFilename . '.' . $file->guessExtension();

                try {
                    // Move the file to the directory where images are stored
                    $file->move(
                        $this->getParameter('uploads_directory'), // Define the directory in services.yaml
                        $newFilename
                    );
                    // Update the image path in the entity
                    $artiste->setImage($newFilename);
                } catch (FileException $e) {
                    // Handle file upload error
                    $this->addFlash('error', 'Failed to upload the image.');
                }
            }

            // Save changes to the database
            $entityManager->flush();

            return $this->redirectToRoute('artiste_index');
        }

        return $this->render('artiste/edit.html.twig', [
            'artiste' => $artiste,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'artiste_delete', methods: ['POST'])]
    public function delete(Request $request, Artiste $artiste, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $artiste->getId(), $request->request->get('_token'))) {
            $entityManager->remove($artiste);
            $entityManager->flush();
        }

        return $this->redirectToRoute('artiste_index');
    }
    #[Route('/artistes', name: 'app_artistes')]
    public function showAllArtistes(ArtisteRepository $artisteRepository): Response
    {

        return $this->render('home/Artiste.html.twig', [
            'artistes' => $artisteRepository->findAll(),
        ]);
    }

}
