<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Controller\Annotations as FOSRest,
    FOS\RestBundle\Controller\Annotations\NamePrefix,
    Nelmio\ApiDocBundle\Annotation\ApiDoc;

use AppBundle\Entity\Post;

/**
 * @NamePrefix("api_v1_")
 */
class PostController extends FOSRestController
{
    /**
    * Get single Page,
    *
    * @ApiDoc(
    *   resource = true,
    *   description = "Gets a Post for a given id",
    *   output = "AppBundle\Entity\Post",
    *   statusCodes = {
    *     200 = "Returned when successful",
    *     404 = "Returned when the page is not found"
    *   }
    * )
    *
    * @FOSRest\View(templateVar="post")
    *
    * @param Post    $post    the post id
    *
    * @return array
    *
    * @throws NotFoundHttpException when page not exist
    */
    public function getPostAction(Post $post)
    {
        $post = $this->container
        ->get('post_handler')
        ->get($post);
        $statusCode = 200;
        $view = $this->view($post, $statusCode);
        return $this->handleView($view);
    }
}
