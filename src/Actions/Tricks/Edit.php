<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Form\Trick\TrickEditType;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class Edit
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var TrickRepository
     */
    private TrickRepository $trickRepository;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flash;

    public function __construct(
        FormFactoryInterface $formFactory,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        TrickRepository $trickRepository,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flash
    )
    {
        $this->formFactory = $formFactory;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->trickRepository = $trickRepository;
        $this->urlGenerator = $urlGenerator;
        $this->flash = $flash;
    }

    /**
     * @Route("/trick/edit/{slug}", name="edit_trick")
     * @param string $slug
     * @param ViewResponder $responder
     * @param Request $request
     * @return Response
     */
    public function __invoke(string $slug, ViewResponder $responder, Request $request): Response
    {
        $trick = $this->trickRepository->findOneBy(['slug' => $slug]);

        if (!$trick) {
            throw new NotFoundHttpException("Trick not found");
        }

        $form = $this->formFactory->createBuilder(TrickEditType::class, $trick)->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newTrick = $form->getData();
            foreach ($newTrick->getVideos() as $video) {
                $video->setTrick($newTrick);
                $this->em->persist($video);
            }
            $this->em->persist($newTrick);
            $this->em->flush();

            $this->flash->add('success', 'Le trick a bien été modifié');

            $url = $this->urlGenerator->generate('show_trick', ['slug' => $trick->getSlug()]);
            return new RedirectResponse($url);
        }

        return $responder('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }
}
