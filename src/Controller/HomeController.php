<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer les produits depuis la base de données
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }
}
