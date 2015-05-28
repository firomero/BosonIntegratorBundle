<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 07/03/2015
 * Time: 15:47
 */
namespace UCI\Boson\IntegratorBundle\Events;
/**
 * Definicion de los eventos de la construccion del mapa
 * Class MapEvents
 * @package UCI\Boson\IntegratorBundle\Events
 */
final class MapEvents  {
    /**
     *Evento que se llama antes de construir el grafo
     */
    const PRE_BUILD_MAP = 'integrator.pre_build_map';
    /**
     *Evento que se llama después de construir el grafo
     */
    const POST_BUILD_MAP = 'integrator.post_build_map';
    /**
     *Evento que se llama antes de salvar en la caché el grafo
     */
    const PRE_CACHE = 'integrator.pre_cache';
    /**
     *Evento que se llama después de salvado el grafo en la caché
     */
    const POST_CACHE = 'integrator.post_cache';
}