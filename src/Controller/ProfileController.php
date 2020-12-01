<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Entity\User;
use App\Form\ProfileEditType;
use App\Repository\CommentRepository;
use App\Repository\FriendshipRepository;
use App\Repository\LikeRepository;
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
     * @param User $user
     * @param PostRepository $postRepository
     * @param FriendshipRepository $friendshipRepository
     * @param CommentRepository $commentRepository
     * @param LikeRepository $likeRepository
     * @return Response
     */
    public function index(Request $request, User $user, PostRepository $postRepository, FriendshipRepository $friendshipRepository, CommentRepository $commentRepository, LikeRepository $likeRepository): Response
    {
        $friendships = $friendshipRepository->findFriendshipByUsers($this->getUser(), $user);

        $form = $this->doFriendship($request, $this->getUser(), $user, $friendships);

        if ($form->isSubmitted()) {
            return $this->redirect($request->getUri());
        }

        $posts = $postRepository->findByUser($user);

        foreach ($posts as $post) {
            $commentCount = count($commentRepository->findCommentsOnPost($post));
            $post->setCommentCount($commentCount);
            $likeCount = count($likeRepository->getLikesOnPost($post));
            $post->setLikeCount($likeCount);
        }

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
    {
        $form = $this->createForm(ProfileEditType::class, $user);

        $form->handleRequest($request);

        if (strlen($form->getData()->getCity()) >= 255 or strlen($form->getData()->getAbout()) >= 255) {
            $this->addFlash('fail', "Neither of the fields can contain more than 255 characters.");
            return $this->redirect($request->getUri());
        }

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $file = $request->files->get('profile_edit')['image'];

            if ($file) {
                $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();

                if (!in_array($file->getClientOriginalExtension(), array('img', 'jpg', 'jpeg', 'png'))) {
                    $file->move(
                        $this->getParameter('dump_dir'),
                        $filename
                    );

                } else {
                    $file->move(
                        $this->getParameter('uploads_dir'),
                        $filename
                    );
                    $user->setImage($filename);
                }

            }

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('profile.view', ['uuid' => $this->getUser()->getUuid()]));
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function doFriendship(Request $request, UserInterface $currentUser, User $user, array $friendShips): FormInterface
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
