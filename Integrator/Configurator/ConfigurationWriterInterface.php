<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 18/12/2014
 * Time: 17:06
 */

namespace IntegratorBundle\Integrator\Configurator;


interface ConfigurationWriterInterface {
    /**
     * @param $path
     * @return mixed
     */
    public function write($path);
} 