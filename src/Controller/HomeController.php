<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository, LikeRepository $likeRepository, CommentRepository $commentRepository): Response
    {
        $posts = $postRepository->findFriendsPosts($this->getUser());
        foreach ($posts as $post) {
            $commentCount = count($commentRepository->findCommentsOnPost($post));
            $post->setCommentCount($commentCount);
            $likeCount = count($likeRepository->getLikesOnPost($post));
            $post->setLikeCount($likeCount);
        }

        return $this->render('home/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
