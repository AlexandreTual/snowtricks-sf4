<?php

namespace App\EventListener;

use App\Entity\Image;
use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadImageListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    private function uploadFile($entity)
    {
        if ($entity instanceof Image) {
            $file = $entity->getLink();
            if ($file instanceof UploadedFile) {
                $entity->setLink($this->uploader->upload($file));
            } elseif ($file instanceof File) {
                $entity->setLink($file->getFilename());
            }
        } elseif ($entity instanceof User) {
            $file = $entity->getPicture();
            if ($file instanceof UploadedFile) {
                $entity->setPicture($this->uploader->upload($file));
            } elseif ($file instanceof File) {
                $entity->setPicture($file->getFilename());
            }
        } else {
            return;
        }
    }  
}
