<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'=> 'Email',
                'attr'=> [ 
                    'placeholder'=> 'Indiquez votre adresse email'
                ]
            ])
            ->add('PlainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [                
                'label' => 'Votre mot de passe',
                'attr'=> [ 
                    'placeholder' => 'Choissisez votre mot de passe'
                ],
                'hash_property_path' => 'password'
                ],
                'second_options' => [
                'label' => 'Confirmez votre mot de passe',
                'attr'=> [ 
                    'placeholder'=> 'Confirmez votre mot de passe'
                    ]
                ],
                'mapped' => false,
            ])
            ->add('firstname', TypeTextType::class, [
                'label'=> 'Prénom',
                'attr'=> [ 
                    'placeholder'=> 'Indiquer votre prénom'
                ]
            ])
            ->add('lastname', TypeTextType::class, [
                'label'=> 'Nom',
                'attr'=> [ 
                    'placeholder'=> 'Indiquer votre adresse nom'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label'=> 'Valider',
                'attr'=> [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email'
                ])
            ],
            'data_class' => User::class,
        ]);

    }
}
