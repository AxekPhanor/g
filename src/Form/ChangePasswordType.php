<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', passwordType::class, [
                'label' => false,
                'attr' => [ 
                    'value' => '●●●●●●●●●●●●',
                    'class' => 'form-control text-white',
                    'style' => 'background-color:#4D5057',
                ]
            ])
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Changer de mot de passe',
                    'attr' => [
                        'class' => 'btn btn-dark'
                    ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
