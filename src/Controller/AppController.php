<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/")
     * @param TrickRepository $trickRepo
     * @param ImageRepository $imageRepo
     * @return array
     * @Template()
     */
    public function index(TrickRepository $trickRepo, ImageRepository $imageRepo)
    {
        return [
            'tricks' => $trickRepo->findBy([], ['id' => 'DESC']),
            'images' => $imageRepo->findAll(),
        ];
    }
}
