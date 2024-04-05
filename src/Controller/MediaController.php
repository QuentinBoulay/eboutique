<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/media')]
class MediaController extends AbstractController
{
    #[Route('/', name: 'app_media_index', methods: ['GET'])]
    public function index(MediaRepository $mediaRepository): Response
    {
        return $this->render('media/index.html.twig', [
            'media' => $mediaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_media_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $medium = new Media();
        $form = $this->createForm(MediaType::class, $medium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du champ file du formulaire
            $file = $form->get('file')->getData();

            // Génération d'un nom de fichier unique
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Déplacement du fichier dans le répertoire de destination
            $file->move(
                $this->getParameter('kernel.project_dir').'/public/uploads/',
                $fileName
            );
            
            // Mise à jour de l'entité avec le nom du fichier
            $medium->setPath($fileName);

            $entityManager->persist($medium);
            $entityManager->flush();

            return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('media/new.html.twig', [
            'medium' => $medium,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_media_show', methods: ['GET'])]
    public function show(Media $medium): Response
    {
        return $this->render('media/show.html.twig', [
            'medium' => $medium,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_media_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Media $medium, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MediaType::class, $medium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('media/edit.html.twig', [
            'medium' => $medium,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_media_delete', methods: ['POST'])]
    public function delete(Request $request, Media $medium, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$medium->getId(), $request->request->get('_token'))) {

            // Vérification qu'il ne soit pas utilisé par un produit ou une catégorie
            $products = $entityManager->getRepository(Product::class)->findBy(['idMedia' => $medium]);
            $categories = $entityManager->getRepository(Category::class)->findBy(['idMedia' => $medium]);

            // Si le média est utilisé, on affiche un message d'erreur
            if (count($products) > 0 || count($categories) > 0) {
                $this->addFlash('danger', 'Impossible de supprimer ce média, il est utilisé par un produit ou une catégorie');
                return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
            }

            // Suppression du fichier 
            if (file_exists($this->getParameter('kernel.project_dir').'/public/uploads/'. $medium->getPath())) {
                unlink($this->getParameter('kernel.project_dir').'/public/uploads/'. $medium->getPath());
            }

            $entityManager->remove($medium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
    }
}
