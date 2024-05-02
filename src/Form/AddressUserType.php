<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddressUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label'=> 'Nom',
            ])
            ->add('lastname', TextType::class, [
                'label'=> 'Prénom',
            ])
            ->add('postal', TextType::class, [
                'label'=> 'Code postal',
            ])
            ->add('city', TextType::class, [
                'label'=> 'Ville',
            ])
            ->add('phone', TextType::class, [
                'label'=> 'Numéro de téléphone',
            ])
            ->add('address', TextType::class, [
                'label'=> 'Adresse',
            ])
            ->add('country', CountryType::class)
            ->add('submit', SubmitType::class, [
                'label'=> 'Ajouter',
                'attr'=> [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
