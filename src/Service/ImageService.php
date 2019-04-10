<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Trick;

class ImageService
{
    private $uploadDirectory;

    public function __construct($uploadDirectory)
    {
        $this->uploadDirectory = $uploadDirectory;
    }

    public function deleteImageInFolder($image)
    {
        if ($image instanceof Image) {
            if (Trick::DEFAULT_IMG === $image->getLink()) {
                return;
            }
            unlink($this->uploadDirectory . '/' . $image->getLink());
        }
    }
}
