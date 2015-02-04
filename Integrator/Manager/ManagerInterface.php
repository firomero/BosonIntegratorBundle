<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 22/12/2014
 * Time: 19:33
 */

namespace IntegratorBundle\Integrator\Manager;


interface ManagerInterface
{
    /**
     * Se obtiene un recurso en específico
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * Paginación
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Adicionar a la lista de recursos
     * @param array $parameters
     * @return mixed
     */
    public function post(array $parameters);

    /**
     * Editar
     * @param ResourceInterface $page
     * @param array $parameters
     * @return mixed
     */
    public function put(ManagerInterface $page, array $parameters);

    /**
     * Editar parcialmente
     * @param ResourceInterface $page
     * @param array $parameters
     * @return mixed
     */
    public function patch(ManagerInterface $page, array $parameters);


} 