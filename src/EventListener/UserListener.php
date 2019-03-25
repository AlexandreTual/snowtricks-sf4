<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserListener
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->updatePassword($entity);
    }

    public  function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $this->updatePassword($entity);
    }

    public function updatePassword($entity){
        if ($entity instanceof User) {
            $entity->setHash($this->encoder->encodePassword($entity ,$entity->getHash()));
        }
    }
}
