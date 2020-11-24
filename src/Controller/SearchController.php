<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     * @param Request $request
     * @param UserRepository $repository
     * @return Response
     */
    public function index(Request $request, UserRepository $repository): Response
    {
        $string = $request->query->get('string');
        $users = $repository->findByString($string);

        return $this->render('search/index.html.twig', [
            'users' => $users
        ]);
    }
}
