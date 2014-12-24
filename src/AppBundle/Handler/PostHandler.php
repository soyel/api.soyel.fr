<?php

namespace AppBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager,
    Symfony\Component\Form\FormFactoryInterface;

use AppBundle\Model\PostInterface,
    AppBundle\Model\PostHandlerInterface,
    AppBundle\Form\Type\PostType,
    AppBundle\Exception\InvalidFormException;

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
     * @var Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    private $formFactory;

    /**
     * @param Doctrine\Common\Persistence\ObjectManager     $om
     * @param AppBundle\Entity\Post                         $entityClass
     * @param Symfony\Component\Form\FormFactoryInterface   $formFactory
     */
    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
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

    /**
     * Create a new Post.
     *
     * @param array $parameters
     *
     * @return PostInterface
     */
    public function post(array $parameters)
    {
        $post = $this->createPost();
        return $this->processForm($post, $parameters, 'POST');
    }

    /**
     * Processes the form.
     *
     * @param PostInterface $post
     * @param array         $parameters
     * @param String        $method
     *
     * @return PostInterface
     *
     * @throws \AppBundle\Exception\InvalidFormException
     */
    private function processForm(PostInterface $post, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new PostType(), $post, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $post = $form->getData();
            $this->om->persist($post);
            $this->om->flush($post);
            return $post;
        }
        throw new InvalidFormException('Invalid submitted data', $form);
    }

    /**
     * Create a new Post.
     *
     * @return PostInterface
     */
    private function createPost()
    {
        return new $this->entityClass();
    }
}
