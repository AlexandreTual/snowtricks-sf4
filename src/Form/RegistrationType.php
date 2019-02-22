<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [ 'placeholder' => 'Votre prénom...']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom de famille...']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [ 'placeholder' => 'Votre adresse email...']
            ])
            ->add('hash', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Vous n'avez pas correctement confirmé votre mot de passe.",
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => ['placeholder' => 'Taper votre nouveau mot de passe...']
                ],
                'second_options' => [
                    'label' => 'Confirmation mot de passe',
                    'attr' => ['placeholder' => 'Confirmer votre nouveau mot de passe..']
                ]
            ])
            ->add('introduction', TextType::class, [
                'label' => 'Introduction',
                'attr' => ['placeholder' => 'Décrivez vous en quelques mots...']
            ])
            ->add('description', TextType::class, [
                'label' => 'Description détaillée',
                'attr' => ['placeholder' => "Lachez vous, c'est le moment de nous parler de vous en détails..."]
            ])
            ->add('picture', UrlType::class, [
                'label' => 'Photo de profile',
                'attr' => ['placeholder' => 'Url de votre avater...']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
