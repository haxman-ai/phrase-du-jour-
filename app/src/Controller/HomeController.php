<?php
namespace App\Controller;

use App\Entity\Sentence;
use App\Repository\SentenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SentenceRepository $sentencerepository): Response
    {    
        $sentences = $sentencerepository->findBy([], ['createdAT' => 'DESC']);
        return $this->render('home/index.html.twig', [
            'sentences' => $sentences,
        ]);
    }

    #[Route('/sentence/{id}', name: 'app_sentence_show')]
    public function show(Sentence $sentence): Response
    {
        return $this->render('home/show.html.twig', [
            'sentence' => $sentence,
        ]);
    }


    #[Route('/likes/{id}',name:'app_like_likes')]
     public function likes(Sentence $sentence, EntityManagerInterface $em): Response
     {   $sentence->setLikes($sentence->getLikes()+1);
         $em->flush();
         return $this->redirectToRoute('app_home',[
            'id'=>$sentence->getId()
         ]);
        

     }

     

    
}


        
    
