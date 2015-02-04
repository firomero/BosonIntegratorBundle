<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 28/12/2014
 * Time: 17:16
 */

namespace IntegratorBundle\Integrator\Configurator;


use IntegratorBundle\Model\Dependency;

class XmlConfiguratorWriter implements ConfigurationWriterInterface{

    /**
     * @param $path
     * @return mixed
     */
    public function write($path)
    {
        // TODO: Implement write() method.
    }

    public function insert(Dependency $dependency, $type)
    {
        // TODO: Implement insert() method.
    }

    /**
     * @param $dependency
     * @param $type
     * @return mixed
     */
    public function dumps($dependency, $type)
    {
        // TODO: Implement dumps() method.
    }
}