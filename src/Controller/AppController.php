<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/")
     * @Template()
     */
    public function index(ObjectManager $manager)
    {
        return [
            'tricks' => $manager->getRepository(Trick::class)->findBy([], ['id' => 'DESC']),
            'images' => $manager->getRepository(Image::class)->findAll(),
        ];
    }
}
