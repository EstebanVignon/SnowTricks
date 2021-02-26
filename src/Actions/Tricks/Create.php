<?php

declare(strict_types=1);

namespace App\Actions\Tricks;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Responders\ViewResponder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
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

    public function __construct(
        FormFactoryInterface $formFactory,
        CategoryRepository $categoryRepository
    )
    {
        $this->formFactory = $formFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/trick/create", name="create_trick")
     * @param ViewResponder $responder
     * @return Response
     */
    public function __invoke(ViewResponder $responder): Response
    {
        $builder = $this->formFactory->createBuilder();

        $builder->add('title', TextType::class, [
            'label' => 'Titre',
            'attr' => [
                'placeholder' => 'Tapez le titre'
            ]
        ])
            ->add('description', TextareaType::class)
            ->add('mainPicture', TextType::class)
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie',
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_value' => 'id',
            ]);

        $builder->setMethod('GET')
            ->setAction('');


        $formBuilder = $builder->getForm();
        $formView = $formBuilder->createView();

        return $responder('trick/create.html.twig', [
            'formView' => $formView
        ]);
    }
}