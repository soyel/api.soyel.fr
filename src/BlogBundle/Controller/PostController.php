<?php

namespace BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Util\Codes,
    FOS\RestBundle\Controller\Annotations as FOSRest,
    Nelmio\ApiDocBundle\Annotation\ApiDoc;

use BlogBundle\Entity\Post,
    BlogBundle\Form\Type\PostType,
    BlogBundle\Exception\InvalidFormException;

/**
 * @FOSRest\NamePrefix("api_v1_")
 */
class PostController extends FOSRestController
{
    /**
     * Get single Post,
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Post for a given id",
     *   output = "BlogBundle\Entity\Post",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the post is not found"
     *   }
     * )
     * @FOSRest\Route(requirements={"_format"="json|xml"})
     * @FOSRest\View(templateVar="post")
     *
     * @param Post    $id    the post id
     *
     * @return array
     *
     * @throws NotFoundHttpException when post not exist
     */
    public function getPostAction(Post $id)
    {
        $post = $this->container
                     ->get('post_handler')
                     ->get($id);
        return $this->view($post, 200);
    }

    /**
     * Create a Post from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new post from the submitted data.",
     *   input = "BlogBundle\Form\Type\PostType",
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
                'id'        => $newPost->getId(),
                '_format'   => $request->get('_format')
            );
            return $this->routeRedirectView('api_v1_get_post', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
    * Update existing post from the submitted data or create a new post at a specific location.
    *
    * @ApiDoc(
    *   resource = true,
    *   input = "Acme\DemoBundle\Form\PostType",
    *   statusCodes = {
    *     201 = "Returned when the Post is created",
    *     204 = "Returned when successful",
    *     400 = "Returned when the form has errors"
    *   }
    * )
    *
    * @FOSRest\View(
    *  templateVar = "form"
    * )
    *
    * @param Request $request the request object
    * @param int     $id      the post id
    *
    * @return FormTypeInterface|View
    *
    * @throws NotFoundHttpException when post not exist
    */
    public function putPostAction(Request $request, $id)
    {
        try {
            if (!($post = $this->container->get('post_handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $post = $this->container->get('post_handler')->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $post = $this->container->get('post_handler')->put(
                    $post,
                    $request->request->all()
                );
            }
            $routeOptions = array(
                'id'        => $post->getId(),
                '_format'   => $request->get('_format')
            );
            return $this->routeRedirectView('api_v1_get_post', $routeOptions, $statusCode);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
    * Update existing post from the submitted data or create a new post at a specific location.
    *
    * @ApiDoc(
    *   resource = true,
    *   input = "Acme\DemoBundle\Form\PostType",
    *   statusCodes = {
    *     204 = "Returned when successful",
    *     400 = "Returned when the form has errors"
    *   }
    * )
    *
    * @FOSRest\View(
    *  templateVar = "form"
    * )
    *
    * @param Request $request the request object
    * @param Post    $id      the post id
    *
    * @return FormTypeInterface|View
    *
    * @throws NotFoundHttpException when post not exist
    */
    public function patchPostAction(Request $request, Post $id)
    {
        try {
            $post = $this->container->get('post_handler')->patch(
                $this->container->get('post_handler')->get($id),
                $request->request->all()
            );
            $routeOptions = array(
                'id' => $post->getId(),
                '_format' => $request->get('_format')
            );
            return $this->routeRedirectView('api_v1_get_post', $routeOptions, Codes::HTTP_NO_CONTENT)
                        ->setHeader('CONTENT_TYPE', 'application/json');
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }
}
