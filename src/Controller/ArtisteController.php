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


    // src/Controller/ArtisteController.php

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

            // Handle the video URLs transformation (from "https://www.youtube.com/watch?v=VIDEO_ID" to embed URL)
            $videoUrls = $form->get('videoUrls')->getData();
            if ($videoUrls) {
                // Convert each URL
                $videoUrlsArray = array_map(function ($url) {
                    return $this->convertToEmbedUrl($url); // Use the helper function to convert URL to embed format
                }, explode(',', $videoUrls));  // Allow multiple URLs, split by commas

                // Join all the embed URLs into a single string (comma-separated)
                $artiste->setVideoUrls(implode(', ', $videoUrlsArray));
            }

            // Persist the new Artiste
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
        // Create the form
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the file upload manually (image)
            $file = $form->get('image')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid() . '-' . $originalFilename . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('uploads_directory'), // The directory path where the image will be stored
                        $newFilename
                    );
                    // Update the image path in the entity
                    $artiste->setImage($newFilename);
                } catch (FileException $e) {
                    // Handle file upload error
                    $this->addFlash('error', 'Failed to upload the image.');
                }
            }

            // Handle video URLs transformation (ensure URLs are in the embed format)
            $videoUrls = $form->get('videoUrls')->getData();
            if ($videoUrls) {
                $convertedUrls = array_map(
                    fn($url) => $this->convertToEmbedUrl(trim($url)),
                    explode(',', $videoUrls)
                );
                $artiste->setVideoUrls(implode(', ', $convertedUrls));
            }

            // Save changes to the database
            $entityManager->flush();

            // Redirect to the artist index after saving
            return $this->redirectToRoute('artiste_index');
        }

        return $this->render('artiste/edit.html.twig', [
            'artiste' => $artiste,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Convert YouTube URL to Embed URL.
     * 
     * @param string $url The YouTube URL.
     * 
     * @return string The embed URL.
     */
    private function convertToEmbedUrl(string $url): string
    {
        $patterns = [
            '/(?:https?:\/\/(?:www\.)?youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/)([a-zA-Z0-9_-]{11}))/i',
            '/(?:https?:\/\/(?:www\.)?youtu\.be\/([a-zA-Z0-9_-]{11}))/i',
            '/(?:https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11}))/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        return $url; // Return original URL if no match
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





}
