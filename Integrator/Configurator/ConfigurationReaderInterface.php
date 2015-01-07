<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 18/12/2014
 * Time: 17:03
 */

namespace IntegratorBundle\Integrator\Configurator;


use Doctrine\Common\Collections\ArrayCollection;

interface ConfigurationReaderInterface {
const DEPENDENCY = 1;
const RESOLVED = 2;

    /**
     * Carga las configuraciones según su tipo
     * @param $path
     * @param $type
     * @return mixed
     */
    public function loadConfiguration($type,$path=null);

    /**
     * Retorna la lista de dependencias
     * @return mixed
     */
    public function getDependencyList();

    /**
     * Retorna la lista de recursos que se brindan
     * @return ArrayCollection
     */
    public function getResolvedList();


} 