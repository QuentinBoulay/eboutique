<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Service\BreadcrumbService;
use App\Entity\Category;


class ShopController extends AbstractController
{
    #[Route('/shop', name: 'app_shop')]
    public function index(EntityManagerInterface $entityManager, BreadcrumbService $breadcrumbService): Response
    {
        $breadcrumbService->add('Accueil', 'app_home');
        $breadcrumbService->add('Boutique', 'app_shop');

        // Récupérer les produits et catégories depuis la base de données
        $products = $entityManager->getRepository(Product::class)->findAll();
        $categories = $entityManager->getRepository(Category::class)->findAll();

        // Ajouter le chemin de l'image pour chaque produit
        foreach ($products as $product) {
            if ($product->getIdMedia() === null) {
                $product->media = "thumbnail-product.svg";
            } else {
                $product->media = $product->getIdMedia()->getPath();
            }
        }

        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'products' => $products,
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs(),
            'categories' => $categories,
        ]);
    }
}
