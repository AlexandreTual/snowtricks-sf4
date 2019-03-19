<?php

namespace App\EventListener;

use App\Entity\Trick;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DateListener
{
    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Trick) {
            $entity->setCreatedAt(new \DateTime());
        }

    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entity->setUpdatedAt(new \DateTime());
    }
}