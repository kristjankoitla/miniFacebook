<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController
{
    public function show(\Throwable $exception): Response
    {
        try {
            $code = $exception->getStatusCode();
        } catch (\Throwable $exception) {
            $code = "500";
        }

        return $this->render('error/index.html.twig', [
            'code' => $code
        ]);
    }
}
