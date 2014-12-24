<?php

namespace BlogBundle\Tests\Handler;

use BlogBundle\Entity\Post,
    BlogBundle\Handler\PostHandler;

class PostHandlerTest extends \PHPUnit_Framework_TestCase
{
    const POST_CLASS = 'BlogBundle\Tests\Handler\DummyPost';

    /**
     * @var BlogBundle\Handler\PostHandler $postHandler
     */
    private $postHandler;

    /**
    * @var Doctrine\Common\Persistence\ObjectManager $om
    */
    private $om;

    /**
    * @var BlogBundle\Entity\Post $entityClass
    */
    private $entityClass;

    /**
    * @var Doctrine\ORM\EntityRepository $repository
    */
    private $repository;

    /**
    * @var Symfony\Component\Form\FormFactoryInterface $formFactory
    */
    private $formFactory;

    public function setUp()
    {
        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
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
        $post = $this->getPost();
        $this->repository->expects($this->once())
             ->method('find')
             ->with($this->equalTo($id))
             ->will($this->returnValue($post));
        $this->postHandler = new PostHandler($this->om, static::POST_CLASS, $this->formFactory);
        $this->postHandler->get($id);
    }

    protected function createPostHandler($objectManager, $postClass, $formFactory)
    {
        return new PageHandler($objectManager, $pageClass, $formFactory);
    }

    protected function getPost()
    {
        $postClass = static::POST_CLASS;
        return new $postClass();
    }
}

class DummyPost extends Post
{
}
