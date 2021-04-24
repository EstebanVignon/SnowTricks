<?php

declare(strict_types=1);

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordLostRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur",
                'required' => true,
                'attr' => [
                    'placeholder' => "Saisir le nom d'utilisateur"
                ]
            ])
            ->setMethod('POST')
            ->setAction('');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PasswordLostRequestDTO::class
        ]);
    }
}
