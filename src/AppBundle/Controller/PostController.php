<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Util\Codes,
    FOS\RestBundle\Controller\Annotations as FOSRest,
    FOS\RestBundle\Controller\Annotations\NamePrefix,
    Nelmio\ApiDocBundle\Annotation\ApiDoc;

use AppBundle\Entity\Post,
    AppBundle\Form\Type\PostType,
    AppBundle\Exception\InvalidFormException;

/**
 * @NamePrefix("api_v1_")
 */
class PostController extends FOSRestController
{
    /**
     * Get single Post,
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Post for a given id",
     *   output = "AppBundle\Entity\Post",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the post is not found"
     *   }
     * )
     *
     * @FOSRest\View(templateVar="post")
     *
     * @param Post    $post    the post id
     *
     * @return array
     *
     * @throws NotFoundHttpException when post not exist
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

    /**
     * Create a Post from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new post from the submitted data.",
     *   input = "AppBundle\Form\Type\PostType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @FOSRest\View(
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postPostAction(Request $request)
    {
        try {
            $newPost = $this->container->get('post_handler')->post(
                $request->request->all()
            );
            $routeOptions = array(
                'post' => $newPost->getId(),
                '_format' => $request->get('_format')
            );
            return $this->routeRedirectView('api_v1_get_post', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }
}
