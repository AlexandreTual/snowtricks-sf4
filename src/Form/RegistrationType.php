<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'form.registration.firstname.label',
                'attr' => [
                    'placeholder' => 'form.registration.firstname.placeholder',
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.registration.lastname.label',
                'attr' => [
                    'placeholder' => 'form.registration.lastname.placeholder',
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.registration.email.label',
                'attr' => [
                    'placeholder' => 'form.registration.email.placeholder',
                ]
            ])
            ->add('hash', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'form.registration.hash.invalid.message',
                'required' => true,
                'first_options' => [
                    'label' => 'form.registration.hash.option.1.label',
                    'attr' => [
                        'placeholder' => 'form.registration.hash.option.1.placeholder',
                    ]
                ],
                'second_options' => [
                    'label' => 'form.registration.hash.option.2.label',
                    'attr' => [
                        'placeholder' => 'form.registration.hash.option.2.placeholder',
                    ]
                ]
            ])
            ->add('introduction', TextType::class, [
                'label' => 'form.registration.introduction.label',
                'attr' => [
                    'placeholder' => 'form.registration.introduction.placeholder',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'form.registration.description.label',
                'attr' => [
                    'placeholder' => 'form.registration.description.placeholder',
                ]
            ])
            ->add('picture', FileType::class, [
                'label' => 'form.registration.picture.label',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
