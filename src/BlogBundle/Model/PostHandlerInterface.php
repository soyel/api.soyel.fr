<?php
namespace BlogBundle\Model;

use BlogBundle\Model\PostInterface;

interface PostHandlerInterface
{
    /**
    * Get a Post given the identifier
    *
    * @api
    *
    * @param mixed $id
    *
    * @return PostInterface
    */
    public function get($id);

    /**
     * Create a new post
     *
     * @api
     *
     * @param array $parameters
     *
     * @return PostInterface
     */
    public function post(array $parameters);
}
