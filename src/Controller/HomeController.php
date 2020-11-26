<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @param PostRepository $postRepository
     * @param LikeRepository $likeRepository
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function index(Request $request, PostRepository $postRepository, LikeRepository $likeRepository, CommentRepository $commentRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add("post", TextareaType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            $post = new Post();
            $post->setUser($this->getUser());
            $post->setText($form->getData()['post']);

            $em->persist($post);
            $em->flush();

            return $this->redirect($request->getUri());
        }

        $posts = $postRepository->findFriendsPosts($this->getUser());
        foreach ($posts as $post) {
            $commentCount = count($commentRepository->findCommentsOnPost($post));
            $post->setCommentCount($commentCount);
            $likeCount = count($likeRepository->getLikesOnPost($post));
            $post->setLikeCount($likeCount);
        }

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'form' => $form->createView()
        ]);
    }
}
