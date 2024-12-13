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
use Symfony\Contracts\Translation\TranslatorInterface;

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
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        ArtisteRepository $artisteRepository, 
        TranslatorInterface $translator
    ): Response {
        // Check if there's already an artist
        if ($artisteRepository->count([]) > 0) {
            $this->addFlash('warning', $translator->trans('artiste.flash.one_artist_allowed'));
            return $this->redirectToRoute('artiste_index');
        }

        $artiste = new Artiste();
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $artiste->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', $translator->trans('artiste.flash.image_upload_failed'));
                }
            }

            // Handle video URLs transformation
            $videoUrls = $form->get('videoUrls')->getData();
            if ($videoUrls) {
                $convertedUrls = array_map(
                    fn($url) => $this->convertToEmbedUrl(trim($url)),
                    explode(',', $videoUrls)
                );
                $artiste->setVideoUrls(implode(', ', $convertedUrls));
            }

            $entityManager->persist($artiste);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('artiste.flash.created_success'));
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
    public function edit(
        Request $request, 
        Artiste $artiste, 
        EntityManagerInterface $entityManager, 
        TranslatorInterface $translator
    ): Response {
        $form = $this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $file = $form->get('image')->getData();
            if ($file) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $artiste->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', $translator->trans('artiste.flash.image_upload_failed'));
                }
            }

            // Handle video URLs transformation
            $videoUrls = $form->get('videoUrls')->getData();
            if ($videoUrls) {
                $convertedUrls = array_map(
                    fn($url) => $this->convertToEmbedUrl(trim($url)),
                    explode(',', $videoUrls)
                );
                $artiste->setVideoUrls(implode(', ', $convertedUrls));
            }

            $entityManager->flush();

            $this->addFlash('success', $translator->trans('artiste.flash.updated_success'));
            return $this->redirectToRoute('artiste_index');
        }

        return $this->render('artiste/edit.html.twig', [
            'form' => $form->createView(),
            'artiste' => $artiste,
        ]);
    }

    #[Route('/{id}', name: 'artiste_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Artiste $artiste, 
        EntityManagerInterface $entityManager, 
        TranslatorInterface $translator
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $artiste->getId(), $request->request->get('_token'))) {
            $entityManager->remove($artiste);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('artiste.flash.deleted_success'));
        }

        return $this->redirectToRoute('artiste_index');
    }

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
}
