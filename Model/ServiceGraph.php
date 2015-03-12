<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 07/02/2015
 * Time: 8:03
 */

namespace IntegratorBundle\Model;


use PlasmaConduit\DependencyGraph;
use PlasmaConduit\dependencygraph\DependencyGraphNode;
use PlasmaConduit\Map;

class ServiceGraph extends DependencyGraph{

    public function __construct()
    {
        $this->_roots    = new ServiceNodeList();
        $this->_registry = new Map();

    }

    /**
     * Retorna la lista de dependencias
     * @param DependencyGraphNode $node
     * @return Map
     */
    public function getDependencias(DependencyGraphNode $node)
    {
        return $this->_roots->getDependencyNodes($node);
    }


    /**
     * Retornna un nodo
     * @param $nodeName
     * @return \PlasmaConduit\option\Option
     */
    public function getNode($nodeName)
    {
        return $this->_roots->findNodeByName($nodeName);
    }


    /**
     * Obtiene la uri del servicio asociado
     * @param  string $dependencyName
     * @throws \Exception
     * @return string
     */
    public function get($dependencyName)
    {
        $mapa = $this->toArray();
        foreach ($mapa as $locate) {
            if (array_key_exists($dependencyName,$locate)) {
                return current($locate[$dependencyName]);
            }
        }

        throw new \Exception('E2');
    }


} 