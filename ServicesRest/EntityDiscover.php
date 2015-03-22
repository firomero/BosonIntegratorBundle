<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 20/03/15
 * Time: 10:51
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;


use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;

trait EntityDiscover {
    /**
     * @param EntityManager $em
     * @param string|object $class
     *
     * @return boolean
     */
    function isEntity(EntityManager $em, $class)
    {
        if(is_object($class)){
            $class = ClassUtils::getClass($class);
        }
        return ! $em->getMetadataFactory()->isTransient($class);
    }
} 