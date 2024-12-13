<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Repository\ArtisteRepository;
use App\Repository\EvenementRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/{_locale}', name: 'app_home', defaults: ['_locale' => 'fr'], requirements: ['_locale' => 'fr|en|de'])]
    public function index(ArtisteRepository $artisteRepository, EvenementRepository $evenementRepository): Response
    {
        // Fetch all artistes from the database
        $artistes = $artisteRepository->findAll();
        $nearestEvents = $evenementRepository->findBy([], ['date' => 'ASC'], 4);

        return $this->render('home/index.html.twig', [
            'artistes' => $artistes,
            'events' => $nearestEvents,
        ]);
    }

    #[Route('/{_locale}/Galerie', name: 'app_galerie', defaults: ['_locale' => 'fr'], requirements: ['_locale' => 'fr|en|de'])]
    public function galerie(ProduitRepository $produitRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Filter products with the category "Galerie"
        $query = $produitRepository->createQueryBuilder('p')
            ->where('p.Categorie = :Categorie')
            ->setParameter('Categorie', 'Galerie')
            ->getQuery();
    
        // Paginate the query
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );
    
        return $this->render('home/galerie.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    

    #[Route('/{_locale}/Galerie/{id}', name: 'app_galerie_detail', defaults: ['_locale' => 'fr'], requirements: ['_locale' => 'fr|en|de', 'id' => '\d+'])]
    public function produitDetail(ProduitRepository $produitRepository, int $id): Response
    {
        // Fetch the product by its ID
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Le produit demandé est introuvable.');
        }

        return $this->render('home/GalItem.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{_locale}/Galerie/categorie/{Categorie}', name: 'app_galerie_categorie', defaults: ['_locale' => 'fr'], requirements: ['_locale' => 'fr|en|de'])]
    public function galerieParCategorie(ProduitRepository $produitRepository, PaginatorInterface $paginator, Request $request, string $Categorie): Response
    {
        // Créez une requête pour filtrer les produits par catégorie
        $query = $produitRepository->createQueryBuilder('p')
            ->where('p.Categorie = :Categorie')
            ->setParameter('Categorie', $Categorie)
            ->getQuery();

        // Paginer la requête
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('home/galerie_par_categorie.html.twig', [
            'pagination' => $pagination,
            'Categorie' => $Categorie,
        ]);
    }

    #[Route('/{_locale}/art', name: 'Artiste_index', defaults: ['_locale' => 'fr'], requirements: ['_locale' => 'fr|en|de'])]
    public function Artiste(ArtisteRepository $artisteRepository): Response
    {
        // Fetch all artistes from the database
        $artistes = $artisteRepository->findAll();

        return $this->render('home/Artiste.html.twig', [
            'artistes' => $artistes,
        ]);
    }

    #[Route('/{_locale}/event', name: 'Event-app', defaults: ['_locale' => 'fr'], requirements: ['_locale' => 'fr|en|de'])]
    public function indexEvent(Request $request, PaginatorInterface $paginator, EntityManagerInterface $entityManager): Response
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

        return $this->render('home/indexE.html.twig', [
            'evenements' => $pagination,
            'lieux' => $lieux,
            'selected_lieu' => $lieuFilter,
            'selected_date_order' => $dateOrder,
        ]);
    }

    #[Route('/{_locale}/event/{id}', name: 'evenement_Details', defaults: ['_locale' => 'fr'], requirements: ['_locale' => 'fr|en|de'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('home/DetailsEvent.html.twig', [
            'evenement' => $evenement,
        ]);
    }
}
