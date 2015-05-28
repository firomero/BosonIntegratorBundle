<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 07/03/2015
 * Time: 15:53
 */

namespace UCI\Boson\IntegratorBundle\Events;


/**
 * Definicion de los eventos del componente de integracion
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

    /**
     *Evento que se lanza cuando se pide un recurso
     */
    const PRE_RETRIEVE = 'integrator.pre_retrieve';

    /**
     *Evcento que se lanza despues que se pide un recurso
     */
    const POST_RETRIEVE = 'integrator.post_retrieve';
}