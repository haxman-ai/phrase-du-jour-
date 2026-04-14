<?php

namespace App\Controller;

use App\Entity\Sentence;
use App\Form\SentenceType;
use App\Repository\SentenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_index')]
    public function index(SentenceRepository $sentenceRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $sentences = $sentenceRepository->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        return $this->render('admin/index.html.twig', [
            'sentences' => $sentences,
        ]);
    }

    #[Route('/admin/new', name: 'app_admin_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $sentence = new Sentence();
        $form = $this->createForm(SentenceType::class, $sentence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sentence->setCreatedAt(new \DateTimeImmutable());
            $sentence->setLikes(0);

            $em->persist($sentence);
            $em->flush();

            $this->addFlash('success', 'phrase du jour ajoutée avec succès!');

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form,
        ]);
    }
}