<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('link', UrlType::class, [
                'label' => 'form.media.link.label',
                'attr' => [
                    'placeholder' => 'form.media.link.placeholder',
                ]
            ])
            ->add('caption', TextType::class , [
                'label' => 'form.media.caption.label',
                'attr' => [
                    'placeholder' => 'form.media.caption.placeholder',
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'form.media.type.label',
                'choices' => [
                    'form.media.type.choices.image' => 'image',
                    'form.media.type.choices.video' => 'video'
                ]
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
