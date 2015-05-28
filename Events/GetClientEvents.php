<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 07/03/2015
 * Time: 16:00
 */

namespace UCI\Boson\IntegratorBundle\Events;


use Symfony\Component\EventDispatcher\Event;

/**
 * Encapsula los objetos a tratar al lanzarse un evento
 * Class GetClientEvents
 * @package UCI\Boson\IntegratorBundle\Events
 */
class GetClientEvents extends Event{

    protected  $app_list;


    public function __construct($applist)
    {
        $this->app_list = $applist;
    }

    /**
     * @return mixed
     */
    public function getAppList()
    {
        return $this->app_list;
    }




} 