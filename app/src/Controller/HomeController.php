<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Sentence;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\SentenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SentenceRepository $sentenceRepository): Response
    {
        $sentences = $sentenceRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'sentences' => $sentences,
        ]);
    }

    #[Route('/sentence/{id}', name: 'app_sentence_show')]
    public function show(
        int $id,
        SentenceRepository $sentenceRepository,
        CommentRepository $commentRepository,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $sentence = $sentenceRepository->find($id);

        if (!$sentence) {
            throw $this->createNotFoundException('Phrase introuvable.');
        }

        $comments = $commentRepository->findBy(
            ['sentence' => $sentence],
            ['createdAt' => 'DESC']
        );

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                throw $this->createAccessDeniedException();
            }

            $comment->setAuthor($this->getUser());
            $comment->setSentence($sentence);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('app_sentence_show', [
                'id' => $sentence->getId(),
            ]);
        }

        return $this->render('home/show.html.twig', [
            'sentence' => $sentence,
            'comments' => $comments,
            'form'     => $form->createView(),
        ]);
    }

    #[Route('/likes/{id}', name: 'app_like_likes')]
    public function likes(Sentence $sentence, EntityManagerInterface $em): Response
    {
        $sentence->setLikes($sentence->getLikes() + 1);
        $em->flush();

        return $this->redirectToRoute('app_sentence_show', [
            'id' => $sentence->getId(),
        ]);
    }
}