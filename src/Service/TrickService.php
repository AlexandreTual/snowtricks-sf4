<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\Common\Persistence\ObjectManager;

class TrickService
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function removeMedia(Trick $trick, $mediaType, $mediaId)
    {
        $className = ucfirst($mediaType);
        $methodName = 'remove' . $className;
        $media = $this->manager->getRepository('App\\Entity\\' . $className)->findOneById($mediaId);
        $trick->$methodName($media);
        $this->manager->flush();
    }

    public function editMedia($form, $trick)
    {
        foreach ($form->get('images')->getData() as $dataImage) {
            $image = new Image();
            $image->setLink($dataImage->getLink())
                ->setCaption($dataImage->getCaption());
            $trick->addImage($image);
        }
        foreach ($form->get('videos')->getData() as $dataVideo) {
            $video = new Video();
            $video->setTag($dataVideo->getTag());
            $trick->addVideo($video);
        }
        $this->manager->flush();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generateToken()
    {
        return md5(random_bytes(10));
    }
}
