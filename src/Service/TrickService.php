<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Timestampable;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Common\Persistence\ObjectManager;

class TrickService
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return object[]
     */
    public function getTricks()
    {
        return $this->manager->getRepository(Trick::class)->findBy([], ['id' => 'DESC']);
    }

    public function add(Trick $trick, User $user)
    {
        $date = new Timestampable();
        $date->setCreatedAt(new \DateTime());
        $trick->setDate($date);
        $trick->setUser($user);
        $this->manager->persist($trick);
        $this->manager->flush();

        return true;
    }

    public function edit(Trick $trick)
    {
        $date = new Timestampable();
        $date->setUpdatedAt(new \DateTime());
        $trick->setDate($date);
        $this->manager->flush();

        return true;
    }

    public function delete(Trick $trick)
    {
        $this->manager->remove($trick);
        $this->manager->flush();

        return true;
    }

    public function removeImage(Trick $trick, $mediaType, $mediaId)
    {
        if ('image' == $mediaType) {
            $image = $this->manager->getRepository(Image::class)->findOneById($mediaId);
            $trick->removeImage($image);
            $this->manager->flush();

            return true;
        }

        return false;
    }

    public function removeVideo(Trick $trick, $mediaType, $mediaId)
    {
        if ('video' == $mediaType) {
            $video = $this->manager->getRepository(Video::class)->findOneById($mediaId);
            $trick->removeImage($video);
            $this->manager->flush();

            return true;
        }

        return false;
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
}
