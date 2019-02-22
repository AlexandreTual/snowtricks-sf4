<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;


class Pagination
{
    private $entityClass;
    private $currentPage = 1;
    private $limit = 10;
    private $manager;

    public function __construct(ObjectManager $manager){
        $this->manager = $manager;
        
    }

    public function getPages()
    {
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        return $pages = ceil($total / $this->limit);
    }

    public function getData(?array $criteria, ?array $orderBy)
    {
        $offset = $this->currentPage * $this->limit - $this->limit;
        $repo = $this->manager->getRepository($this->entityClass);

        return $repo->findBy($criteria, $orderBy, $this->limit, $offset);
    }

    /**
     * Get the value of currentPage
     */ 
    public function getPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the value of currentPage
     *
     * @return  self
     */ 
    public function setPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Get the value of limit
     */ 
    public function getlimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */ 
    public function setlimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of entityClass
     */ 
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set the value of entityClass
     *
     * @return  self
     */ 
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get the value of repo
     */ 
    public function getRepo()
    {
        return $this->repo;
    }

    /**
     * Set the value of repo
     *
     * @return  self
     */ 
    public function setRepo($repo)
    {
        $this->repo = $repo;

        return $this;
    }
}
