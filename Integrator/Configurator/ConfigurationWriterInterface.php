<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 18/12/2014
 * Time: 17:06
 */

namespace IntegratorBundle\Integrator\Configurator;


use IntegratorBundle\Model\Dependency;

interface ConfigurationWriterInterface {
    /**
     * @param $path
     * @return mixed
     */
    public function write($path);

    public function insert(Dependency $dependency, $type);

    /**
     * @param $dependency
     * @param $type
     * @return mixed
     */
    public function dumps( $dependency, $type);
} 