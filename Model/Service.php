<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 12/03/15
 * Time: 14:16
 */

namespace IntegratorBundle\Model;


use UCI\Boson\SeguridadBundle\Entity\Recurso;

final class Service extends Recurso {

    private $uri;

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }
} 