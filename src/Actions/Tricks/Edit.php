<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Entity\Trick;
use App\Form\Trick\TrickCreateType;
use App\Form\Trick\TrickEditDTO;
use App\Form\Trick\TrickEditType;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use App\Responders\ViewResponder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class Edit
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
    /**
     * @var TrickRepository
     */
    private $trickRepository;

    public function __construct(
        FormFactoryInterface $formFactory,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        TrickRepository $trickRepository
    )
    {
        $this->formFactory = $formFactory;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/trick/{id}/edit", name="edit_trick")
     * @param string $id
     * @param ViewResponder $responder
     * @param Request $request
     * @return Response
     */
    public function __invoke(string $id, ViewResponder $responder, Request $request): Response
    {
        $trick = $this->trickRepository->findOneBy(['id' => $id]);

        if (!$trick) {
            throw new NotFoundHttpException("Trick not found");
        }

        $dto = TrickEditDTO::createFromEntity($trick);

        $form = $this->formFactory->createBuilder(TrickEditType::class, $dto)->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $trick->create($data->title, $data->description, $data->mainPicture);
            $trick->setCategory($data->category);
            $this->em->persist($trick);
            $this->em->flush();
        }

        $formView = $form->createView();

        return $responder('trick/edit.html.twig', [
            'formView' => $formView,
            'trick' => $trick
        ]);
    }
}