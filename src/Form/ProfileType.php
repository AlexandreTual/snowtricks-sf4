<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'form.profile.firstname.label',
                'attr' => [
                    'placeholder' => 'form.profile.firstname.placeholder',
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.profile.lastname.label',
                'attr' => [
                    'placeholder' => 'form.profile.lastname.placeholder'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.profile.email.label',
                'attr' => [
                    'placeholder' => 'form.profile.email.placeholder'
                ]
            ])
            ->add('introduction', TextType::class, [
                'label' => 'form.profile.email.introduction.label',
                'attr' => [
                    'placeholder' => 'form.profile.email.introduction.placeholder'
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'form.profile.email.description.label',
                'attr' => [
                    'placeholder' => 'form.profile.email.description.placeholder'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
