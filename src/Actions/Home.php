<?php

declare(strict_types=1);

namespace App\Actions;

use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class Home
{
    /**
     * @var TrickRepository
     */
    private TrickRepository $repository;

    /**
     * @var Environment
     */
    private Environment $twig;

    public function __construct(
        TrickRepository $repository,
        Environment $twig
    ) {
        $this->repository = $repository;
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="homepage")
     * @param ViewResponder $responder
     * @param Request $request
     * @return Response
     */
    public function __invoke(ViewResponder $responder, Request $request): Response
    {
        $tricksNumber = 8;
        if ($request->query->get('ajax')) {
            $currentTricksNbr = $request->query->get('currentTricks');
            $tricks = $this->repository->getTricksWithFilters($tricksNumber, $currentTricksNbr);
            return new JsonResponse([
                'content' => $this->twig->render('_tricks.html.twig', [
                    'tricks' => $tricks
                ])
            ]);
        }
        $tricks = $this->repository->getTricksWithFilters($tricksNumber, 0);
        return $responder('homepage.html.Twig', ['tricks' => $tricks]);
    }
}
