<?php

namespace App\DataFixtures;

use App\Core\Utils;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
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

        // Users creation
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();

            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = $faker->email;
            $hash = 'password';
            $introduction = $faker->sentence;
            $description = $faker->text(1000);
            $roles = ['ROLE_USER'];
            $picture = 'c6a39f8d1a1f8e44b2c362efde817a06.jpeg';

            $user->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setHash($this->encoder->encodePassword($user, $hash))
                ->setIntroduction($introduction)
                ->setDescription($description)
                ->setPicture($picture)
                ->setRoles($roles);
            $manager->persist($user);
            $users[] = $user;
        }

        //Administrator creation
        $admin = new User();

        $firstName = 'admin';
        $lastName = 'snowtricks';
        $email = 'admin@snowtricks.com';
        $hash = 'password';
        $introduction = $faker->sentence;
        $description = $faker->text(2000);
        $roles = ['ROLE_ADMIN'];
        $picture = 'c6a39f8d1a1f8e44b2c362efde817a06.jpeg';

        $admin->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email)
            ->setHash($this->encoder->encodePassword($admin, $hash))
            ->setIntroduction($introduction)
            ->setDescription($description)
            ->setPicture($picture)
            ->setRoles($roles);
        $manager->persist($admin);

        // Categories creation
        for ($l = 0; $l <= 5; $l++) {
            $category = new Category();
            $category->setName($faker->sentence(rand(1,3)))
                ->setDescription($faker->paragraph());
            $categories[] = $category;
            $manager->persist($category);
        }

        //Tricks creation
        for ($j = 0; $j <= 20; $j++) {
            $trick = new Trick();
            $name = $faker->name;
            $description = '<p>' . join("</p><p>", $faker->paragraphs(3)) . '</p>';
            for ($k = 0; $k <= rand(1, 5); $k++) {
                $image = new Image();
                $image->setLink('ef49d9f3afde6345307e0bef6293399b.jpg')
                    ->setCaption($faker->sentence());
                $trick->addImage($image);
            }

            for ($m = 0; $m <= rand(0, 3); $m++) {
                $video = new Video();
                $tag = '<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/UtZofZjccBs"></iframe>';
                $video->setTag($tag);
                $trick->addVideo($video);
            }

            for ($n = 0; $n <= 20; $n++) {
                $comment = new Comment();
                $comment->setContent($faker->sentence);
                $trick->addComment($comment);
                $user = $faker->randomElement($users);
                $user->addComment($comment);
            }

            $trick->setName($name)
                ->setDescription($description)
                ->setUser($faker->randomElement($users))
            ->setCategory($faker->randomElement($categories));

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
