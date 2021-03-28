<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Form\Trick\TrickCreateType;
use App\Repository\CategoryRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class Create
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
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flash
    )
    {
        $this->formFactory = $formFactory;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->flash = $flash;
    }

    /**
     * @Route("/trick/create", name="create_trick")
     * @param ViewResponder $responder
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function __invoke(ViewResponder $responder, Request $request)
    {
        $form = $this->formFactory->createBuilder(TrickCreateType::class)->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            foreach ($trick->getVideos() as $video) {
                $this->em->persist($video);
            }
            $this->em->persist($trick);
            $this->em->flush();

            $this->flash->add('success', 'Le trick a bien été créé');

            $url = $this->urlGenerator->generate('show_trick', ['slug' => $trick->getSlug()]);
            return new RedirectResponse($url);
        }

        return $responder('trick/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
