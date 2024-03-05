<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\BreadcrumbService;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\CommandLine;
use Symfony\Component\HttpFoundation\Request;


class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $entityManager, Security $security, BreadcrumbService $breadcrumbService): Response
    {
        // Breadcrumb
        $breadcrumbService->add('Accueil', 'app_home');
        $breadcrumbService->add('Panier', 'app_cart');

        // Récupérer le panier de l'utilisateur
        $basket =  $entityManager->getRepository(Order::class)->findOneBy(
            [
                'idUser' => $security->getUser()->getId(),
                'valid' => false
            ]
        );

        $basketDetails = $basket->getCommandLines();

        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'basket' => $basket,
            'basketDetails' => $basketDetails,
        ]);
    }

    #[Route('/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Logique pour ajouter l'article au panier
        if ($this->isCsrfTokenValid('add_to_cart'.$product->getId(), $request->request->get('_token'))) {
            // Récupérer le panier de l'utilisateur
            $basket =  $entityManager->getRepository(Order::class)->findOneBy(
                [
                    'idUser' => $this->getUser()->getId(),
                    'valid' => false
                ]
            );

            // Vérifier si le produit est déjà dans le panier
            $basketDetails = $basket->getCommandLines();
            $productInBasket = false;
            foreach ($basketDetails as $basketDetail) {
                if ($basketDetail->getIdProduct() == $product) {
                    $productInBasket = true;
                    $basketDetail->setQuantity($basketDetail->getQuantity() + 1);
                    $entityManager->persist($basketDetail);
                }
            }

            // Si le produit n'est pas dans le panier, on l'ajoute
            if (!$productInBasket) {
                $basketDetail = new CommandLine();
                $basketDetail->setIdProduct($product);
                $basketDetail->setQuantity(1);
                $basketDetail->setIdOrder($basket);
                $entityManager->persist($basketDetail);
            }

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cart'); 
    }
}
