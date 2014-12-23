<?php

namespace AppBundle\Tests\Fixtures\Entity;

use Doctrine\Common\DataFixtures\FixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Post;

class LoadPostData implements FixtureInterface
{
    static public $posts = array();

    public function load(ObjectManager $manager)
    {
        $post = new Post();
        $post->setTitle('title');
        $post->setContent('content');
        $manager->persist($post);
        $manager->flush();
        self::$posts[] = $post;
    }
}
