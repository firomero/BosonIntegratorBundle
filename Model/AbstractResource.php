<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 12/03/15
 * Time: 14:03
 */

namespace UCI\Boson\IntegratorBundle\Model;
use Symfony\Component\DependencyInjection\Container;


/**
 * Clase abstracta que permite que algo se comporte como recurso rest.
 * Class AbstractResource
 * @package IntegratorBundle\Model
 */
abstract class AbstractResource {


    protected $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    /**
     * Devuelve un Recurso dado un identificador
     * @param $id
     * @return $this
     */
    public function get($id)
    {
        return $this;
    }

    /**
     * Devuelve una lista de recursos a partir de criterios
     * @param array $filtros
     * @return array
     */
    public function getList($filtros=array())
    {
      return array();
    }

    /**
     * Modifica un valor a partir de valores de entrada
     * @param array $values
     * @return array
     */
    public function put($values = array())
    {
        return array();
    }

    /**
     * Crea un nuevo recurso a partir de valores
     * @param array $values
     * @return array
     */
    public function post($values= array())
    {
        return array();
    }

    /**
     * Elimina un recurso a partir de un id
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return true;
    }




} 