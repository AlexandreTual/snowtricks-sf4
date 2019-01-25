<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.trick.name.label',
                'attr' => [
                    'placeholder' => 'form.trick.name.placeholder',
                    'autofocus' => true,
                ]])
            ->add('introduction', TextType::class, [
                'label' => 'form.trick.introduction.label',
                'attr' => [
                    'placeholder' => 'form.trick.introduction.placeholder',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'form.trick.description.label',
                'attr' => [
                    'placeholder' => 'form.trick.description.placeholder',
                ]
            ])
            ->add('coverImage', UrlType::class, [
                'label' => 'form.trick.coverImage.label',
                'attr' => [
                    'placeholder' => 'form.trick.coverImage.placeholder',
                ]
            ])
            ->add('media', CollectionType::class, [
                'label' => 'form.trick.media.label',
                'entry_type' => MediaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
