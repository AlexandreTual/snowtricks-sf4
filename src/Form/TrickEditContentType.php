<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickEditContentType extends AbstractType
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
            ->add('description', TextareaType::class, [
                'label' => 'form.trick.description.label',
                'attr' => [
                    'placeholder' => 'form.trick.description.placeholder',
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'form.trick.category.label',
                'class' => Category::class,
                'choice_label' => 'name',
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
