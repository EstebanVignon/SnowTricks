<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Form\Trick\TrickEditDTO;
use App\Form\Trick\TrickEditType;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function __construct(
        FormFactoryInterface $formFactory,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        TrickRepository $trickRepository,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->formFactory = $formFactory;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->trickRepository = $trickRepository;
        $this->urlGenerator = $urlGenerator;
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

        $dto = TrickEditDTO::createFromEntity($trick);

        $form = $this->formFactory->createBuilder(TrickEditType::class, $dto)->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $trick->update($data->title, $data->description, $data->mainPicture);
            $trick->setCategory($data->category);
            $this->em->persist($trick);
            $this->em->flush();

            $url = $this->urlGenerator->generate('show_trick', ['slug' => $trick->getSlug()]);
            return new RedirectResponse($url);
        }

        $formView = $form->createView();

        return $responder('trick/edit.html.twig', [
            'formView' => $formView,
            'trick' => $trick
        ]);
    }
}