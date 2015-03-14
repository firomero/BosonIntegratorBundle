<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 07/03/2015
 * Time: 16:00
 */

namespace UCI\Boson\IntegratorBundle\Events;


use Symfony\Component\EventDispatcher\Event;

class GetClientEvents extends Event{

    protected  $app_list;


    public function __construct(array $applist)
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