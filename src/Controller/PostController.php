<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function create(Request $request, UserInterface $user)
    {
        $post = new Post();
        $post->setUser($user);

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($post);
            $em->flush();
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
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
