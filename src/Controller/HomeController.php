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
    #[Route('/produit/{id}', name: 'app_produit_detail', requirements: ['id' => '\d+'])]
    public function produitDetail(ProduitRepository $produitRepository, int $id): Response
    {
        // Fetch the product by its ID
        $produit = $produitRepository->find($id);

        // Check if the product exists
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        return $this->render('home/produit_detail.html.twig', [
            'produit' => $produit,
        ]);
    }



}
