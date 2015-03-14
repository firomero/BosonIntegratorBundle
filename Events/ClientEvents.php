<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 07/03/2015
 * Time: 15:53
 */

namespace UCI\Boson\IntegratorBundle\Events;


/**
 * Class ClientEvents
 * @package UCI\Boson\IntegratorBundle\Events
 */
final class ClientEvents {
    /**
     *Evento que se lanza antes de recuperar la información de cada servidor del entorno.
     */
    const PRE_FETCH = 'integrator.pre_fetch';
    /**
     *Evento que se lanza después de recuperar la información de cada servidor del entorno.
     */
    const POST_FETCH = 'integrator.post_fetch';
}