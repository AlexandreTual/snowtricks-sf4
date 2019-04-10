<?php

namespace App\EventListener;

use App\Entity\Image;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DeleteImageListener
{
    private $uploadDirectory;

    public function __construct($uploadDirectory)
    {
        $this->uploadDirectory = $uploadDirectory;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Image) {
            unlink($this->uploadDirectory . '/' . $entity->getLink());
        }
    }
}
