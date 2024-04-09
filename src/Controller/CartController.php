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
use Symfony\Component\HttpFoundation\JsonResponse;


class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $entityManager, Security $security, BreadcrumbService $breadcrumbService): Response
    {
        // Breadcrumb
        $breadcrumbService->add('Accueil', 'app_home');
        $breadcrumbService->add('Panier', 'app_cart');

        // Récupérer le panier de l'utilisateur
        $basket = $entityManager->getRepository(Order::class)->findOneBy(
            [
                'idUser' => $security->getUser()->getId(),
                'valid' => false
            ]
        );

        $basketDetails = null;

        if ($basket) {
            // Récupérer les détails du panier
            $basketDetails = $basket->getCommandLines();
            $basket->totalPrice = 0;
            
            if ($basketDetails) {
                foreach ($basketDetails as $key => $value) {
                    $product = $value->getIdProduct();

                    if ($product->getIdMedia() === null) {
                        $product->media = "thumbnail-product.svg";
                    } else {
                        $product->media = $product->getIdMedia()->getPath();
                    }

                    $basketDetails[$key]->product = $product;
                    $basket->totalPrice += $product->getPriceHT() * $value->getQuantity();
                }
            }
        }
            
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'hideQuantityInput' => false,
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

            if ($basket) {
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
            } else {
                // Si l'utilisateur n'a pas de panier, on en crée un
                $basket = new Order();
                $basket->setIdUser($this->getUser());
                $basket->setValid(false);
                $basket->setOrderNumber(uniqid());
                $basket->setDateTime(new \DateTime());
                $entityManager->persist($basket);

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

    #[Route('/validate/{id}', name: 'app_cart_validate', methods: ['POST'])]
    public function validateCart(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        // Logique pour ajouter l'article au panier
        if ($this->isCsrfTokenValid('validate_cart'.$order->getId(), $request->request->get('_token'))) {
            $order->setValid(true);
            $order->setDateTime(new \DateTime());
            $entityManager->persist($order);
            $entityManager->flush();
        }   

        return $this->redirectToRoute('app_cart'); 
    }

    #[Route('/empty/{id}', name: 'app_cart_empty', methods: ['POST'])]
    public function emptyCart(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        // Logique pour retirer tous les articles du panier
        if ($this->isCsrfTokenValid('empty_cart'.$order->getId(), $request->request->get('_token'))) {
            $orderDetails = $order->getCommandLines();
            if (!empty($orderDetails)) {
                foreach ($orderDetails as $orderDetail) {
                    $order->removeCommandLine($orderDetail);
                    $entityManager->remove($orderDetail);
                }
                $entityManager->flush();
            }
        } else {
            throw new \Exception('Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/update-cart', name: 'update_cart', methods: ['GET'])]
    public function updateCart(Request $request, EntityManagerInterface $entityManager, Security $security)
    {
         // Récupéreration des infos
         $newQuantity = $request->query->get('quantity');
         $price = $request->query->get('price');
         $id = $request->query->get('id');
         $priceTotal = $request->query->get('total');

         // Modification des quantité de l'entité
         $basket = $entityManager->getRepository(Order::class)->findOneBy(
            [
                'idUser' => 1,
                'valid' => false
            ]
        );

        $total = 0;


        if ($basket) {
            $basketDetails = $basket->getCommandLines();

            foreach ($basketDetails as $basketDetail) {
                if ($basketDetail->getIdProduct()->getId() == $id) {
                    $basketDetail->setQuantity($newQuantity);
                    $entityManager->persist($basketDetail);
                    $entityManager->flush();
                }

                $total += $basketDetail->getIdProduct()->getPriceHT() * $basketDetail->getQuantity();
            }

            // Récupération du total du panier
        }

        // Renvoi du nouveau prix du panier pour l'affichage en live
        $newPrice = $total;

        return new JsonResponse(['new_price' => $newPrice, 'new_quantity' => $basket]);
    }
}
