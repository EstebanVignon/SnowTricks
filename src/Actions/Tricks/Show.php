<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class Show
{
    /**
     * @var TrickRepository
     */
    private $repository;

    public function __construct(
        TrickRepository $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/trick/{slug}", name="show_trick")
     * @param $slug
     * @param ViewResponder $responder
     * @return Response
     */
    public function __invoke($slug, ViewResponder $responder): Response
    {
        $trick = $this->repository->findOneBy(['slug' => $slug]);

        if (!$trick) {
            throw new NotFoundHttpException('This trick does not exist');
        }

        return $responder('trick/single.html.twig', [
            'trick' => $trick
        ]);
    }
}