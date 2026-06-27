<?php

namespace App\Controller;

use App\Entity\Webnovels;
use App\Form\WebnovelsType;
use App\Repository\WebnovelsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/webnovels')]
final class WebnovelsController extends AbstractController
{
    #[Route(name: 'app_webnovels_index', methods: ['GET'])]
    public function index(WebnovelsRepository $webnovelsRepository): Response
    {
        return $this->render('webnovels/index.html.twig', [
            'webnovels' => $webnovelsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_webnovels_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $webnovel = new Webnovels();
        $form = $this->createForm(WebnovelsType::class, $webnovel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($webnovel);
            $entityManager->flush();

            return $this->redirectToRoute('app_webnovels_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('webnovels/new.html.twig', [
            'webnovel' => $webnovel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_webnovels_show', methods: ['GET'])]
    public function show(Webnovels $webnovel): Response
    {
        return $this->render('webnovels/show.html.twig', [
            'webnovel' => $webnovel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_webnovels_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Webnovels $webnovel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WebnovelsType::class, $webnovel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_webnovels_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('webnovels/edit.html.twig', [
            'webnovel' => $webnovel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_webnovels_delete', methods: ['POST'])]
    public function delete(Request $request, Webnovels $webnovel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$webnovel->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($webnovel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_webnovels_index', [], Response::HTTP_SEE_OTHER);
    }
}
