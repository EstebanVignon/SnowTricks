<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Entity\Comment;
use App\Form\Comments\AddCommentType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
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

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flash;
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        TrickRepository $repository,
        CommentRepository $commentRepository,
        Environment $twig,
        FormFactoryInterface $formFactory,
        Security $security,
        EntityManagerInterface $em,
        FlashBagInterface $flash,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->repository = $repository;
        $this->commentRepository = $commentRepository;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->security = $security;
        $this->em = $em;
        $this->flash = $flash;
        $this->urlGenerator = $urlGenerator;
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

        $form = $this->formFactory->createBuilder(AddCommentType::class)->getForm()->handleRequest($request);


        $currentUser = $this->security->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $commentDTO = $form->getData();

            $comment = new Comment();
            $comment->setUser($currentUser);
            $comment->setTrick($trick);
            $comment->setContent($commentDTO->content);

            $this->em->persist($comment);
            $this->em->flush();

            $this->flash->add('success', 'Votre commentaire à bien été ajouté');

            $url = $this->urlGenerator->generate('show_trick', ['slug' => $trick->getSlug()]);
            return new RedirectResponse($url);
        }

        $commentsNumber = 5;
        if ($request->query->get('ajax')) {
            $currentCommentsNbr = $request->query->get('currentComments');
            $currentCommentsNbr = (int)$currentCommentsNbr;
            $comments = $this->commentRepository->getCommentsWithFilters($trick, $commentsNumber, $currentCommentsNbr);
            return new JsonResponse([
                'content' => $this->twig->render('trick/_comments.html.twig', [
                    'currentUser' => $currentUser,
                    'comments' => $comments
                ])
            ]);
        } else {
            $comments = $this->commentRepository->getCommentsWithFilters($trick, $commentsNumber, 0);
            return $responder('trick/single.html.twig', [
                'currentUser' => $currentUser,
                'form' => $form->createView(),
                'trick' => $trick,
                'comments' => $comments
            ]);
        }
    }
}
