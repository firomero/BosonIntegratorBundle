<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 12/03/15
 * Time: 14:16
 */

namespace UCI\Boson\IntegratorBundle\Model;
use UCI\Boson\IntegratorBundle\Annotation\RestService;

/**
 * @RestService(name="servicio",domain="integrador",allow={"GET"})
 * */
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