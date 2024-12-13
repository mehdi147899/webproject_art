<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(Request $request, PaginatorInterface $paginator, EntityManagerInterface $entityManager): Response
    {
        $lieuFilter = $request->query->get('lieu', null); // Get the selected lieu
        $dateOrder = $request->query->get('date_order', 'asc'); // Get the date order, default to ascending

        // Build the query
        $queryBuilder = $entityManager->getRepository(Evenement::class)
            ->createQueryBuilder('e');

        // Apply lieu filter if selected
        if ($lieuFilter) {
            $queryBuilder->andWhere('e.lieu = :lieu')
                ->setParameter('lieu', $lieuFilter);
        }

        // Order by date
        $queryBuilder->orderBy('e.date', $dateOrder);

        // Paginate results
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            9
        );

        // Fetch all unique lieux for the filter dropdown
        $lieux = $entityManager->getRepository(Evenement::class)
            ->createQueryBuilder('e')
            ->select('DISTINCT e.lieu')
            ->getQuery()
            ->getResult();

        return $this->render('evenement/index.html.twig', [
            'evenements' => $pagination,
            'lieux' => $lieux,
            'selected_lieu' => $lieuFilter,
            'selected_date_order' => $dateOrder,
        ]);
    }
    #[Route('/evenement/new', name: 'evenement_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);

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
                $evenement->setImage($newFilename);
            }

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement', ['id' => $evenement->getId()]);
        }

        return $this->render('evenement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evenement/{id}', name: 'evenement_show')]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/evenement/{id}/edit', name: 'evenement_edit')]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
                $evenement->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('evenement_show', ['id' => $evenement->getId()]);
        }

        return $this->render('evenement/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event/{id}/delete', name: 'evenement_delete')]

    public function delete(Evenement $event, EntityManagerInterface $em): Response
    {
        $em->remove($event);
        $em->flush();

        $this->addFlash('success', 'Event deleted successfully.');

        return $this->redirectToRoute('app_evenement');
    }

}
