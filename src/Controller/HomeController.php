<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param PostRepository $repository
     * @return Response
     */
    public function index(PostRepository $repository): Response
    {
        $posts = $repository->findAll();
        return $this->render('home/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
