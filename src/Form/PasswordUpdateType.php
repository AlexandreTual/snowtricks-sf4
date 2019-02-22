<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr' => ['placeholder' => 'Taper le mot de passe que vous utilisez actuellement...']
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Vous n'avez pas correctement confirmé votre mot de passe.",
                'constraints' => new Length(['min' => 6, 'minMessage' => 'Le mot de passe doit contenir six caractères minimum.']),
                'required' => true,
                'first_options' => ['label' => 'Nouveau mot de passe', 'attr' => ['placeholder' => 'Taper votre nouveau mot de passe...']],
                'second_options' => ['label' => 'Confirmation mot de passe', 'attr' => ['placeholder' => 'Confirmer votre nouveau mot de passe..']]
            ])
        ;
    }
}
