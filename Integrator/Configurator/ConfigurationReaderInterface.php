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
public function loadConfiguration($path);
public function getDependencyList();

    /**
     * @return ArrayCollection
     */
    public function getResolvedList();
} 