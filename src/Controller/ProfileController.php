<?php

namespace App\Controller;

use App\Form\ProfileEditType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @Route("/profile", name="profile.")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param UserInterface $user
     * @param PostRepository $repository
     * @return Response
     */
    public function index(UserInterface $user, PostRepository $repository): Response
    {
        $posts = $repository->findByUser($user->getUsername());

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/edit", name="edit")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function edit(Request $request, UserInterface $user): Response
    {
        $form = $this->createForm(ProfileEditType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
