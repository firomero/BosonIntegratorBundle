<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 07/02/2015
 * Time: 8:12
 */

namespace IntegratorBundle\Model;


use PlasmaConduit\dependencygraph\DependencyGraphNodes;

class ServiceNodeList extends DependencyGraphNodes {

    /**
     * Encuentra un nodo por el nombre
     * @param $nodeName
     * @return \PlasmaConduit\option\Option
     */
    public function findNodeByName($nodeName)
    {
        return $this->getAllSiblings()->findValue(/**
         * @param $value
         * @param $key
         * @return bool
         */
        function($value, $key) use($nodeName) {
            return $value->getName() == $nodeName;
        });

    }

} 