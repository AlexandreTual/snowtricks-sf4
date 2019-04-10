<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TrickService
{
    private $manager;
    private $imageService;

    public function __construct(ObjectManager $manager, ImageService $imageService)
    {
        $this->manager = $manager;
        $this->imageService = $imageService;
    }

    public function removeMedia(Trick $trick, $mediaType, $mediaId)
    {
        $className = ucfirst($mediaType);
        $methodName = 'remove' . $className;
        $media = $this->manager->getRepository('App\\Entity\\' . $className)->findOneById($mediaId);
        $trick->$methodName($media);
        $this->manager->flush();
        if ('image' == $mediaType) {
            $this->imageService->deleteImageInFolder($media);
        }
    }

    public function addMedia($form, $trick)
    {
        foreach ($form->get('images')->getData() as $dataImage) {
            $image = new Image();
            $image->setLink($dataImage->getLink())
                ->setCaption($dataImage->getCaption());
            $trick->addImage($image);
        }
        foreach ($form->get('videos')->getData() as $dataVideo) {
            $video = new Video();
            $video->setTag($dataVideo->getResponsiveTag());
            $trick->addVideo($video);
        }
        $this->manager->flush();
    }
}
