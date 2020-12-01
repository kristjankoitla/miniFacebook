<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $uuid = strtolower($data['first_name']) . '-' . strtolower($data['last_name']);
            $userCount = count($userRepository->findByUuid($uuid));
            if ($userCount >= 1) {
                $uuid = $uuid . '-' . $userCount;
            }

            if (count($userRepository->findByEmail($data['email'])) >= 1) {
                $this->addFlash('fail', 'Email "' . $data['email'] . '" is taken');
                return $this->redirect($this->generateUrl('register'));
            }

            if (strlen($data['email']) >= 255 or strlen($data['first_name']) >= 255 or strlen($data['last_name']) >= 255) {
                $this->addFlash('fail', "None of the fields can be longer than 255 characters");
                return $this->redirect($request->getUri());
            }

            $user = new User();
            $user->setEmail($data['email']);
            $user->setFirstName($data['first_name']);
            $user->setLastName($data['last_name']);
            $user->setUuid($uuid);
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $data['password'])
            );

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('app_login'));
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
