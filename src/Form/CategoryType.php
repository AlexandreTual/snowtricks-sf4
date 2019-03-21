<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.category.name.label',
                'attr' => [
                    'placeholder' => 'form.category.name.placeholder',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'form.category.description.label',
                'attr' => [
                    'placeholder' => 'form.category.description.placeholder',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'word.save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
