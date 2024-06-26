<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Service\BreadcrumbService;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order, Security $security, BreadcrumbService $breadcrumbService): Response
    {
        // Vérifie si l'utilisateur en cours est l'utilisateur ciblé
        if ($security->getUser()->getId() !== $order->getIdUser()->getId() && !$security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        // Breadcrumb
        $breadcrumbService->add('Accueil', 'app_home');
        $breadcrumbService->add('Mon compte', 'app_user_show', ['id' => $security->getUser()->getId()]);
        $breadcrumbService->add('Commande N°' . $order->getOrderNumber(), 'app_order_show', ['id' => $order->getId()]);

        // Récupération des items de la commande
        if ($order) {
            // Récupérer les détails du panier
            $orderDetails = $order->getCommandLines();
            $order->totalPrice = 0;
            
            foreach ($orderDetails as $key => $value) {
                $product = $value->getIdProduct();

                if ($product->getIdMedia() === null) {
                    $product->media = "thumbnail-product.svg";
                } else {
                    $product->media = $product->getIdMedia()->getPath();
                }

                $orderDetails[$key]->product = $product;
                $order->totalPrice += $product->getPriceHT() * $value->getQuantity();
            }
        }

        return $this->render('order/show.html.twig', [
            'hideQuantityInput' => true,
            'order' => $order,
            'orderDetails' => $orderDetails,
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
