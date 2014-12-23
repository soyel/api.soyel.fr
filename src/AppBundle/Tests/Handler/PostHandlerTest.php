<?php

namespace AppBundle\Tests\Handler;

use AppBundle\Entity\Post,
    AppBundle\Handler\PostHandler;

class PostHandlerTest extends \PHPUnit_Framework_TestCase
{
    const POST_CLASS = 'AppBundle\Tests\Handler\DummyPost';

    private $postHandler;

    private $om;

    private $repository;

    public function setUp()
    {
        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::POST_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::POST_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::POST_CLASS));
    }

    public function testGet()
    {
        $id = 1;
        $post = new Post();
        $this->repository->expects($this->once())
             ->method('find')
             ->with($this->equalTo($id))
             ->will($this->returnValue($post));
        $this->postHandler = new PostHandler($this->om, static::POST_CLASS);
        $this->postHandler->get($id);
    }
}

class DummyPost extends Post
{
}
