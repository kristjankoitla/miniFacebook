<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
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
}
