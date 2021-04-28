<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class Show
{
    /**
     * @var TrickRepository
     */
    private TrickRepository $repository;
    /**
     * @var CommentRepository
     */
    private CommentRepository $commentRepository;

    /**
     * @var Environment
     */
    private Environment $twig;

    public function __construct(
        TrickRepository $repository,
        CommentRepository $commentRepository,
        Environment $twig
    ) {
        $this->repository = $repository;
        $this->commentRepository = $commentRepository;
        $this->twig = $twig;
    }

    /**
     * @Route("/trick/show/{slug}", name="show_trick")
     * @param $slug
     * @param ViewResponder $responder
     * @param Request $request
     * @return Response
     */
    public function __invoke($slug, ViewResponder $responder, Request $request): Response
    {

        $trick = $this->repository->findOneBy(['slug' => $slug]);
        if (!$trick) {
            throw new NotFoundHttpException('This trick does not exist');
        }

        $commentsNumber = 5;
        if ($request->query->get('ajax')) {
            $currentCommentsNbr = $request->query->get('currentComments');
            $currentCommentsNbr = (int)$currentCommentsNbr;
            $comments = $this->commentRepository->getCommentsWithFilters($trick, $commentsNumber, $currentCommentsNbr);
            return new JsonResponse([
                'content' => $this->twig->render('trick/_comments.html.twig', [
                    'comments' => $comments
                ])
            ]);
        } else {
            $comments = $this->commentRepository->getCommentsWithFilters($trick, $commentsNumber, 0);

            return $responder('trick/single.html.twig', [
                'trick' => $trick,
                'comments' => $comments
            ]);
        }
    }
}
