<?php

namespace App\Controller;

use App\Entity\Category;
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
    public function categoriesList(ObjectManager $manager)
    {
        $categories = $manager->getRepository(Category::class)->findAll();

        return [
            'categories' => $categories
        ];
    }
}
