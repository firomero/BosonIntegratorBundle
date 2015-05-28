<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 20/03/15
 * Time: 9:29
 */

namespace UCI\Boson\IntegratorBundle\Events;


use Doctrine\Common\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use UCI\Boson\IntegratorBundle\Manager\IntegratorKernel;

/**
 * Suscriber que se registra al lanzar eventos de recuperacion
 * Class RetrieveSuscriber
 * @package UCI\Boson\IntegratorBundle\Events
 */
class RetrieveSuscriber implements EventSubscriberInterface{

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            ClientEvents::PRE_RETRIEVE => 'onPreRetrieve'
        );
    }

    /**
     * @param GetClientEvents $events
     */
    public function onPreRetrieve(GetClientEvents $events)
    {
        /**
         * @var IntegratorKernel $kernel
        */
        $kernel = $events->getAppList();

        $new = $kernel->isNew();
        if ($new) {
            $kernel->buildMap();
        }


    }
}