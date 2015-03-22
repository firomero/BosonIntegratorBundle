<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 21/03/15
 * Time: 14:17
 */

namespace UCI\Boson\IntegratorBundle\Model;


use UCI\Boson\ExcepcionesBundle\Exception\LocalException;

use UCI\Boson\IntegratorBundle\Annotation\RestService;

/**
 * @RestService(name="exception",domain="integrador",allow={"GET"})
 * */
class IntegratorException extends LocalException{

} 