<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'form.passwordUpdate.oldPassword.label',
                'attr' => [
                    'placeholder' => 'form.passwordUpdate.oldPassword.placeholder',
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'form.passwordUpdate.password.invalid.message',
                'constraints' => new Length([
                    'min' => 6,
                    'minMessage' => 'form.passwordUpdate.password.constraints',
                ]),
                'required' => true,
                'first_options' => [
                    'label' => 'form.passwordUpdate.password.option.1.label',
                    'attr' => [
                        'placeholder' => 'form.passwordUpdate.password.option.1.placeholder',
                    ]
                ],
                'second_options' => [
                    'label' => 'form.passwordUpdate.password.option.2.label',
                    'attr' => [
                        'placeholder' => 'form.passwordUpdate.password.option.2.placeholder',
                    ]
                ]
            ])
        ;
    }
}
