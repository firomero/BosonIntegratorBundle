<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 28/12/2014
 * Time: 17:21
 */

namespace IntegratorBundle\Integrator\Manager;


class ResolvedManager implements ManagerInterface {

    /**
     * Se obtiene un recurso en específico
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        // TODO: Implement get() method.
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