<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Entity\Trick;
use App\Form\Trick\TrickCreateDTO;
use App\Form\Trick\TrickCreateType;
use App\Repository\CategoryRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function __construct(
        FormFactoryInterface $formFactory,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->formFactory = $formFactory;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
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
            $data = $form->getData();
            $trick = new Trick();
            $trick->create($data->title, $data->description, $data->mainPicture);
            $trick->setCategory($data->category);
            $this->em->persist($trick);
            $this->em->flush();

            $url = $this->urlGenerator->generate('show_trick', ['slug' => $trick->getSlug()]);
            return new RedirectResponse($url);
        }

        $formView = $form->createView();

        return $responder('trick/create.html.twig', [
            'formView' => $formView
        ]);
    }
}