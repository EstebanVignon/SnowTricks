<?php

declare(strict_types=1);

namespace App\Actions;

use App\Responders\ViewResponder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Hello
{
    /**
     * @param string $firstname
     * @param ViewResponder $responder
     * @return Response
     * @Route("/hello/{firstname<\D+>?World}", name="hello", methods={"GET", "POST"})
     */
    public function __invoke(string $firstname, ViewResponder $responder): Response
    {
        return $responder('hello.html.twig', [
            'firstname' => $firstname
        ]);
    }
}