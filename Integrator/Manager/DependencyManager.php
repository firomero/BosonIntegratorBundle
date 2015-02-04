<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 28/12/2014
 * Time: 17:21
 */

namespace IntegratorBundle\Integrator\Manager;


use IntegratorBundle\Integrator\Configurator\ConfigurationReaderInterface;
use IntegratorBundle\Integrator\Configurator\ConfigurationWriterInterface;

class DependencyManager implements ManagerInterface{

  protected $reader;
  protected $writer;

    /**
     * @param ConfigurationReaderInterface $configurationReaderInterface
     * @param ConfigurationWriterInterface $configurationWriterInterface
     */
    public function __construct(ConfigurationReaderInterface $configurationReaderInterface, ConfigurationWriterInterface $configurationWriterInterface)
    {
        $this->reader = $configurationReaderInterface;
        $this->writer = $configurationWriterInterface;

    }

    /**
     * Se obtiene un recurso en específico
     * @param $name
     * @return mixed
     */
    public function get($name=null)
    {
       $list =  $this->reader->getDependencyList();
        if (isset($name)) {
            return $list;
        }

        $data = array();
        foreach ($list as $item) {
            if ($item->getName()==$name) {
                return $item;
            }
        }

        return $data;
    }

    /**
     * Paginación
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function all($limit = 5, $offset = 0)
    {
        // TODO: Implement all() method.
    }

    /**
     * Adicionar a la lista de recursos
     * @param array $parameters
     * @return mixed
     */
    public function post(array $parameters)
    {
        // TODO: Implement post() method.
    }

    /**
     * Editar
     * @param ResourceInterface $page
     * @param array $parameters
     * @return mixed
     */
    public function put(ManagerInterface $page, array $parameters)
    {
        // TODO: Implement put() method.
    }

    /**
     * Editar parcialmente
     * @param ResourceInterface $page
     * @param array $parameters
     * @return mixed
     */
    public function patch(ManagerInterface $page, array $parameters)
    {
        // TODO: Implement patch() method.
    }
}