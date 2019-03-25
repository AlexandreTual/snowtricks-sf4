<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecurityPasswordUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hash', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'form.updatePassword.hash.invalid.message',
                'required' => true,
                'first_options' => [
                    'label' => 'form.updatePassword.hash.option.1.label',
                    'attr' => [
                        'placeholder' => 'form.updatePassword.hash.option.1.placeholder',
                    ]
                ],
                'second_options' => [
                    'label' => 'form.updatePassword.hash.option.2.label',
                    'attr' => [
                        'placeholder' => 'form.updatePassword.hash.option.2.placeholder',
                    ]
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
