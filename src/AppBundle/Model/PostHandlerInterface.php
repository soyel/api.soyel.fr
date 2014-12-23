<?php
namespace AppBundle\Model;

use AppBundle\Model\PostInterface;

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
}
