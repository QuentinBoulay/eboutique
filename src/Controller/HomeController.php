<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Category;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer les produits et catégories depuis la base de données
        $products = $entityManager->getRepository(Product::class)->findAll();

        foreach ($products as $product) {
            if ($product->getIdMedia() === null) {
                $product->media = "thumbnail-product.svg";
            } else {
                $product->media = $product->getIdMedia()->getPath();
            }
        }

        $categories = $entityManager->getRepository(Category::class)->findAll();

        foreach ($categories as $category) {
            if ($category->getIdMedia() === null) {
                $category->media = "thumbnail-product.svg";
            } else {
                $category->media = $category->getIdMedia()->getPath();
            }
        }

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
