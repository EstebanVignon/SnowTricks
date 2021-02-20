<?php

declare(strict_types=1);

namespace App\Actions;

use App\Responders\ViewResponder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Home
{
    /**
     * @param ViewResponder $responder
     * @return Response
     * @Route("/", name="homepage")
     */
    public function __invoke(ViewResponder $responder): Response
    {
        return $responder('homepage.html.Twig', []);
    }
}