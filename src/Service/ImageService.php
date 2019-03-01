<?php

namespace App\Service;

use App\Entity\Image;
use Doctrine\Common\Persistence\ObjectManager;

class ImageService
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function getImages()
    {
        return $this->manager->getRepository(Image::class)->findAll();
    }
}