<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/")
     * @Template()
     */
    public function index(ObjectManager $manager, TrickRepository $trickRepo, ImageRepository $imageRepo, SessionInterface $session)
    {
        return [
            'tricks' => $trickRepo->findBy([], ['id' => 'DESC']),
            'images' => $imageRepo->findAll(),
        ];
    }
}
