<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/list")
     * @Template()
     */
    public function categoriesList(ObjectManager $manager, CategoryRepository $categoryRepo)
    {
        $categories = $categoryRepo->findAll();

        return [
            'categories' => $categories
        ];
    }
}
