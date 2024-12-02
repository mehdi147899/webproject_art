<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
    #[Route('/Galerie', name: 'app_galerie')]
    public function galerie(ProduitRepository $produitRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $produitRepository->createQueryBuilder('p')->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('home/galerie.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/Galerie/{id}', name: 'app_galerie_detail', requirements: ['id' => '\d+'])]
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
    
    #[Route('/Galerie/categorie/{Categorie}', name: 'app_galerie_categorie')]
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
            $request->query->getInt('page', 1), // Numéro de la page actuelle
            9 // Nombre de produits par page
        );
    
        return $this->render('home/galerie_par_categorie.html.twig', [
            'pagination' => $pagination,
            'Categorie' => $Categorie,
        ]);
    }
    
    

}
