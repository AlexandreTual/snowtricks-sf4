<?php

namespace App\DataFixtures;

use App\Core\Utils;
use App\Entity\Media;
use App\Entity\Trick;
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

        $types  = ['image', 'video'];

        for ($h = 0; $h <= 20; $h++) {
            $trick = new Trick();

            $name = $faker->name;
            $description = '<p>' . join("</p><p>", $faker->paragraphs(3)) . '</p>';
            $coverImage = $faker->imageUrl(1000, 400);
            $introduction = $faker->sentence;

            $trick->setName($name)
                ->setSlug(Utils::slugMaker($name))
                ->setDescription($description)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction);

            $media = [];
            for ($j = 0; $j <= mt_rand(2,10); $j++) {
                $media = new Media();
                $type = $faker->randomElement($types);
                $image = $faker->imageUrl(450, 450);
                $video = 'https://www.youtube.com/embed/Zc8Gu8FwZkQ';
                $link = ($type == 'image' ? $image : $video);
                $caption = $faker->sentence;
                $media->setLink($link)
                    ->setCaption($caption)
                    ->setType($type);
                $trick->addMedium($media);

                $manager->persist($media);
            }

            $manager->persist($trick);
        }

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

