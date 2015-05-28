<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 20/02/15
 * Time: 9:54
 */

namespace UCI\Boson\IntegratorBundle\Manager;


use UCI\Boson\IntegratorBundle\Exception\IntegratorException;

/**
 * Herramienta para la lectura de configuraciones yml
 * Interface ConfigurationManagerInteface
 * @package UCI\Boson\IntegratorBundle\Manager
 */
interface ConfigurationManagerInteface {


    /**
     * Este metodo es el que carga un contenido desde la fuente de quien la implemente y devuelve una coleccion con sus valores.
     * @param null $file
     * @throws IntegratorException
     * @return mixed
     */
    public function fileAsArray($file=null);

    /**
     * Este metodo es el encargado de recibir un array y traducirlo a su fuente original
     * @param array $array
     * @throws IntegratorException
     * @return mixed
     */
    public function arrayAsFile(array $array,$uri=null);

} 