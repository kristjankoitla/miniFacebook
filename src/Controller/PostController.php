<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{

    /**
     * @Route("/show/{id}", name="show")
     * @param Request $request
     * @param Post $post
     * @param CommentRepository $commentRepository
     * @param LikeRepository $likeRepository
     * @return Response
     */
    public function show(Request $request, Post $post, CommentRepository $commentRepository, LikeRepository $likeRepository)
    {
        $comments = $commentRepository->findCommentsOnPost($post);
        $likeCount = count($likeRepository->getLikesOnPost($post));

        $userLike = $likeRepository->getUserLikeForPost($this->getUser(), $post);
        if (count($userLike) >= 1) {
            $label = 'unlike';
        } else {
            $label = 'like';
        }

        $likeForm = $this->createFormBuilder()
            ->add($label, SubmitType::class)
            ->getForm();

        $likeForm->handleRequest($request);

        if ($likeForm->isSubmitted() && $likeForm->isValid()) {
//            dump($likeForm); die;
            $em = $this->getDoctrine()->getManager();

            if (count($userLike) >= 1) {
                $em->remove($userLike[0]);
            } else {
                $like = new Like();
                $like->setUser($this->getUser());
                $like->setPost($post);
                $em->persist($like);
            }
            $em->flush();
            return $this->redirect($request->getUri());
        }

        $commentForm = $this->createFormBuilder()
            ->add('comment', TextareaType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $comment = new Comment();
            $comment->setUser($this->getUser());
            $comment->setPost($post);
            $comment->setText($commentForm->getData()['comment']);

//            dump($comment); die;
            $em->persist($comment);
            $em->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'likeForm' => $likeForm->createView(),
            'likeCount' => $likeCount,
            'commentForm' => $commentForm->createView()
        ]);
    }

}
