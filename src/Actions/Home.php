<?php

declare(strict_types=1);

namespace App\Actions;

use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Home
{
    /**
     * @var TrickRepository
     */
    private TrickRepository $repository;

    public function __construct(
        TrickRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="homepage")
     * @param ViewResponder $responder
     * @return Response
     */
    public function __invoke(ViewResponder $responder): Response
    {
        $tricks = $this->repository->findAll();

        return $responder('homepage.html.Twig', ['tricks' => $tricks]);
    }
}
