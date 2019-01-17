<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $genres = ['men', 'women'];

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();

            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = $faker->email;
            $hash = $this->encoder->encodePassword($user, 'password');
            $introduction = $faker->sentence;
            $description = '<p>' . join("</p><p>", $faker->paragraphs(3)) . '</p>';
            $roles = ['ROLE_USER'];

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = '/' . mt_rand(0, 99) . '.jpg';
            $genre = $faker->randomElements($genres);
            $picture .= ($genre = 'men' ? 'men' : 'women') . $pictureId;

            $user->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setHash($hash)
                ->setIntroduction($introduction)
                ->setDescription($description)
                ->setPicture($picture)
                ->setRoles($roles);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
