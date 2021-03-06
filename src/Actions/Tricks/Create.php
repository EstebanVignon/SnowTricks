<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Entity\Trick;
use App\Form\Trick\TrickCreationFormType;
use App\Repository\CategoryRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Create
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        FormFactoryInterface $formFactory,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em
    )
    {
        $this->formFactory = $formFactory;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
    }

    /**
     * @Route("/trick/create", name="create_trick")
     * @param ViewResponder $responder
     * @param Request $request
     * @return Response
     */
    public function __invoke(ViewResponder $responder, Request $request): Response
    {
        $builder = $this->formFactory->createBuilder(TrickCreationFormType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $trick = new Trick();
            $trick->create($data->title, $data->description, $data->mainPicture);
            $trick->setCategory($data->category);
            $this->em->persist($trick);
            $this->em->flush();
        }

        $formView = $form->createView();

        return $responder('trick/create.html.twig', [
            'formView' => $formView
        ]);
    }
}