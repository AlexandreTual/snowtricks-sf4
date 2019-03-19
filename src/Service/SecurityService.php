<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class SecurityService
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function registration($user)
    {
        $user->setStatus($this->tokenGenerate());
        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function tokenGenerate()
    {
        return md5(uniqid());
    }
}