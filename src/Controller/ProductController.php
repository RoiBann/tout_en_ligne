<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\SearchType;
use App\Service\Search;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/nos-produits', name: 'products')]
    public function index(Request $request): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        // Gère la requête du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, recherche les produits avec les critères de recherche
        if ($form->isSubmitted() && $form->isValid()){
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        } else {
            // Sinon, récupère tous les produits
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        // Affiche la page de produits avec les résultats de recherche ou tous les produits
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }


    #[Route('/produit/{slug}', name: 'product')]
    public function show($slug): Response
    {
        // Recherche un produit par son slug dans la base de données
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        // Si le produit n'est pas trouvé, redirige vers la page des produits
        if (!$product){
            return $this->redirectToRoute('products');
        }

        // Affiche la page du produit
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
