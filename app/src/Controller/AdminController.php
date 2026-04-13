<?php
namespace App\Controller;

use App\Repository\SentenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}