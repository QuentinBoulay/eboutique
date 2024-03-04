<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Entity\Category;


class ShopController extends AbstractController
{
    #[Route('/shop', name: 'app_shop')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer les produits et catégories depuis la base de données
        $products = $entityManager->getRepository(Product::class)->findAll();
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
