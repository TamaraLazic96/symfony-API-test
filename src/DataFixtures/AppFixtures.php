<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const users = [
        [
            'username' => 'tami96',
            'password' => 'tamiQueen96',
            'fullName' => 'Tamara Queen',
            'email' => 'tami@test.com',
        ]
    ];

    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /** @var Factory  */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create();
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
        $this->loadBlogPost($manager);
        $this->loadComment($manager);
    }

    private function loadUser(ObjectManager $manager)
    {
        foreach (self::users as $user) {
            $newUser = new User();
            $newUser->setEmail($user['email']);

            $newUser->setPassword($this->encoder->encodePassword(
                $newUser,
                $user['password']
            ));

            $newUser->setUsername($user['username']);
            $newUser->setFullName($user['fullName']);

            $manager->persist($newUser);
        }
        $manager->flush();
    }

    private function loadBlogPost(ObjectManager $manager)
    {
        for($i = 0; $i < 80; $i++) {
            $newPost = new BlogPost();
            $newPost->setTitle($this->faker->realText(30));
            $newPost->setContent($this->faker->realText());
            $newPost->setSlug($this->faker->slug . $i);
            $newPost->setPublished($this->faker->dateTimeThisYear);

            /** @var User $user */
            $user = $manager->getRepository(User::class)
                ->findOneBy(['username' => 'tami96']);

            $newPost->setAuthor($user);

            $this->setReference("blog_post_$i", $newPost);

            $manager->persist($newPost);
        }
        $manager->flush();
    }

    private function loadComment(ObjectManager $manager)
    {
        for($i = 0; $i < 80; $i++) {

            $rand = rand(1,10);

            for($j = 0; $j < $rand; $j++){
                $newComment = new Comment();
                $newComment->setContent($this->faker->realText(30));
                $newComment->setPublished($this->faker->dateTimeThisYear);

                /** @var User $user */
                $user = $manager->getRepository(User::class)
                    ->findOneBy(['username' => 'tami96']);
                $newComment->setAuthor($user);

                /** @var BlogPost $blogPost */
                $blogPost = $this->getReference("blog_post_$i");
                $newComment->setBlogPost($blogPost);

                $manager->persist($newComment);
            }
        }
        $manager->flush();
    }
}
