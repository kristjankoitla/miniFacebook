<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('post/index.html.twig');
    }

    /**
     * @Route("/create", name="create")
     */
    public function create()
    {
        $post = new Post();

        $post->setTitle("");

        $em = $this->getDoctrine()->getManager();

        $em->persist($post);
        $em->flush();

        return new Response("al");
    }

    /**
     * @Route("/remove/{id}", name="remove")
     * @param Post $post
     */
    public function delete(Post $post)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($post);
        $em->flush();

        return $this->redirect($this->generateUrl('home'));
    }
}
