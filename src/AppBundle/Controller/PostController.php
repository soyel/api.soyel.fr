<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Controller\Annotations\NamePrefix;

use AppBundle\Entity\Post;

/**
 * @NamePrefix("api_v1_")
 */
class PostController extends FOSRestController
{
    /**
     * @Route("/")
     */
    public function getPostAction(Post $post)
    {
        return $this->render('default/index.html.twig');
    }
}
