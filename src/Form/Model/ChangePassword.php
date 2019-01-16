<?php

namespace App\Form\Model;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ChangePassword
{
    public function LoadValidatorData(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint(
            'oldPassword',
            new SecurityAssert\UserPassword([
                'message' => "Ce n'est pas votre mot de passe actuel.",
            ])
        );
    }
}