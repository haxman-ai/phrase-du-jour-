<?php

namespace App\Controller;

use App\Entity\Sentence;
use App\Form\CommentType;
use App\Form\SentenceType;
use App\Repository\SentenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Comment;
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

            $this->addFlash('success', 'Phrase du jour ajoutée avec succès !');

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => false,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_admin_edit')]
    public function edit(Request $request, Sentence $sentence, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(SentenceType::class, $sentence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La phrase a bien été modifiée.');

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => true,
        ]);
    }


    #[Route('/admin/{id}/delete',name: 'app_admin_delete')]
    public function delete(Sentence $sentence,EntityManagerInterface $em): Response
    {  
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($sentence);
        $em->flush();
        $this-> addFlash('success', 'la phrase a bien été supprimée.');

        return $this->redirectToRoute('app_admin_index');



    }








}