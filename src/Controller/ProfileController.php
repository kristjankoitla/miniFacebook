<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Entity\User;
use App\Form\ProfileEditType;
use App\Repository\FriendshipRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
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
     * @Route("/view/{uuid}", name="view")
     * @param Request $request
     * @param UserInterface $currentUser
     * @param User $user
     * @param PostRepository $postRepository
     * @param FriendshipRepository $friendshipRepository
     * @return Response
     */
    public function index(Request $request, UserInterface $currentUser, User $user, PostRepository $postRepository, FriendshipRepository $friendshipRepository): Response
    {
        $friendships = $friendshipRepository->findFriendshipByUserIds($currentUser->getUsername(), $user->getUsername());

        $form = $this->doFriendship($request, $currentUser, $user, $friendships);

        $posts = $postRepository->findByUser($user->getUsername());

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
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
    // todo: do I need a firewall rule here?
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

    public function doFriendship(Request $request, UserInterface $currentUser, User $user, Array $friendShips): FormInterface
    {
        if (count($friendShips) <= 0) {
            $label = 'send_friend_request';
        } elseif ($friendShips[0]->getStatus() == 1) {
            $label = 'unfriend';
        } elseif ($friendShips[0]->getInitiator() == $currentUser) {
            $label = 'cancel_friend_request';
        } else {
            $label = 'accept_friend_request';
        }

        $form = $this->createFormBuilder()
            ->add($label, SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted()) {
            if (count($friendShips) <= 0) {
                $friendship = new Friendship();
                $friendship->setInitiator($currentUser);
                $friendship->setReceiver($user);
                $friendship->setStatus(0);
                $em->persist($friendship);
            } elseif ($friendShips[0]->getStatus() == 1 or $friendShips[0]->getInitiator() == $currentUser) {
                $em->remove($friendShips[0]);
            } else {
                $friendShips[0]->setStatus(1);
                $em->persist($friendShips[0]);
            }
            $em->flush();
        }

        return $form;
    }
}
