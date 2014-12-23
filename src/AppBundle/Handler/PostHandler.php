<?php

namespace AppBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager,
    AppBundle\Model\PostInterface,
    AppBundle\Model\PostHandlerInterface;

class PostHandler implements PostHandlerInterface
{
    /**
     * @var Doctrine\Common\Persistence\ObjectManager $om
     */
    private $om;

    /**
     * @var AppBundle\Entity\Post $entityClass
     */
    private $entityClass;

    /**
     * @var Doctrine\ORM\EntityRepository $repository
     */
    private $repository;

    /**
     * @param Doctrine\Common\Persistence\ObjectManager $om
     * @param AppBundle\Entity\Post                     $entityClass
     */
    public function __construct(ObjectManager $om, $entityClass)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
    }

    /**
    * Get a Post.
    *
    * @param mixed $id
    *
    * @return PostInterface
    */
    public function get($id)
    {
        return $this->repository->find($id);
    }
}
