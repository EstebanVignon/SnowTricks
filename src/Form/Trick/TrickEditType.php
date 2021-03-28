<?php

namespace App\Form\Trick;

use App\Entity\Category;
use App\Entity\Trick;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickEditType extends AbstractType
{
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Saisir le nom du trick'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Saisir la description du trick'
                ]
            ])
            ->add('mainPicture', TextType::class, [
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie',
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'choice_value' => 'id'
                //'multiple' => true,
                //'expanded' => true,
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => TrickVideoType::class,
                //'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->setMethod('POST')
            ->setAction('');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class
        ]);
    }
}
