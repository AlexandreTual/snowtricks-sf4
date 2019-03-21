<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryService
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function getAll()
    {
        return $this->manager->getRepository(Category::class)->findAll();
    }
}
