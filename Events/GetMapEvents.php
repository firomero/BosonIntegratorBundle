<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 07/03/2015
 * Time: 15:57
 */

namespace UCI\Boson\IntegratorBundle\Events;


use PlasmaConduit\DependencyGraph;
use Symfony\Component\EventDispatcher\Event;

class GetMapEvents extends Event{

    protected $dependencyGraph;


    public function __construct(DependencyGraph $dependencyGraph)
    {
        $this->dependencyGraph = $dependencyGraph;
    }

    /**
     * @return DependencyGraph
     */
    public function getDependencyGraph()
    {
        return $this->dependencyGraph;
    }
} 