<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\CustomerAddress;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\BreadcrumbService;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, EntityManagerInterface $entityManager, Security $security, BreadcrumbService $breadcrumbService): Response
    {
        $breadcrumbService->add('Accueil', 'app_home');
        $breadcrumbService->add('Mon compte', 'app_user_show', ['id' => $user->getId()]);
        
        // Vérifie si l'utilisateur en cours est l'utilisateur ciblé
        if ($security->getUser()->getId() !== $user->getId() && !$security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        $address =  $entityManager->getRepository(CustomerAddress::class)->findBy(
            ['idUser' => $user->getId()]
        );

        $orders =  $entityManager->getRepository(Order::class)->findBy(
            [
                'idUser' => $user->getId(),
                'valid' => true
            ]
        );

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'address' => $address,
            'orders' => $orders,
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Vérifie si l'utilisateur en cours est l'utilisateur ciblé
        if ($security->getUser()->getId() !== $user->getId() && !$security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, Security $security): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {

            // Vérifie si l'utilisateur en cours est l'utilisateur ciblé
            if ($security->getUser()->getId() !== $user->getId() && !$security->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_user_show', ['id' => $security->getUser()->getId()], Response::HTTP_SEE_OTHER);
            }
        
            $entityManager->remove($user);
            $entityManager->flush();
        }

        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }
}
