<?php


namespace IntegratorBundle\Manager;

use GuzzleHttp\Client;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use UCI\Boson\ExcepcionesBundle\Exception\LocalException;
use UCI\Boson\IntegratorBundle\Events\ClientEvents;
use UCI\Boson\IntegratorBundle\Events\GetClientEvents;

/**
 * Class IntegratorKernel
 * @package IntegratorBundle\Manager
 */
class IntegratorKernel {

    /**
     * @var mixed $config
     */
    protected $config;


    /**
     *@var ServiceManager $serviceManager
     * */
    protected $serviceManager;


    protected $eventDispatcher;


    protected $recursos;


    protected $container ;

    /**
     * @param $server
     * @param $client
     */
    public function __construct($server, $client,ServiceManager $serviceManager, EventDispatcherInterface $dispatcher, Container $container )
    {

        $this->config['server']=$server;

        $this->config['client']=$client;

        $this->serviceManager = $serviceManager;

        $this->serviceManager->setEventDispatcher($dispatcher);

        $this->eventDispatcher = $dispatcher;

        $this->recursos = $this->CargarRecursos();

        $this->container = $container;
    }


    /**
     * Cargar los recursos registrados en el ecosistema definido
     * @return array
     */
    public function CargarRecursos()
    {
        $is_server = $this->config['server']['is_server'];
        $recursos = array();
        if ($is_server) {

            $applist = $this->config['server']['app_list'];
            $clientEvents = new GetClientEvents($applist);
            $this->eventDispatcher->dispatch(ClientEvents::PRE_FETCH,$clientEvents);
            foreach ($applist as $app) {
                array_push($recursos, $this->Representaciones($app));
            }

            $clientEvents = new GetClientEvents($applist);
            $this->eventDispatcher->dispatch(ClientEvents::POST_FETCH,$clientEvents);

        }

        return $recursos;
    }

    /**
     *Genera el mapa de dependencias
     */
    public function buildMap()
    {

        $this->serviceManager->buildDependecyGraph($this->recursos);
    }

    /**
     * Recupera un servicio.
     * @param $domain
     * @param $name
     * @return mixed|string
     * @throws \UCI\Boson\ExcepcionesBundle\Exception\LocalException
     */
    public function findService($domain,$name)
    {
       $servicio = $this->serviceManager->getServicio(array(
           'name'=>$name,
           'domain'=>$domain
       ));

        return $servicio;
    }

    /**
     * Obtiene los servicios y dependendencias de una aplicacion
     * @param $url
     * @throws LocalException
     * @return mixed
     */
    public function Representaciones($url)
    {
        $client = new Client();
        $response = $client->get($url);
        if ($response->isSuccessful()) {
            $json = $response->json();

            return $json;
        }
        else
        {

            /** @var Logger $logger*/
            $logger = $this->container->get('logger');
            $logger->addAlert('TimeOut');

        }
        throw new LocalException('E7',true);
    }

    /**
     * Retorna la url del servicio solicitado
     * @param $dependencia
     * @return mixed|string
     * @throws \UCI\Boson\ExcepcionesBundle\Exception\LocalException
     */
    public function getURI($dependencia)
    {
        return $this->serviceManager->getServicio($dependencia);
    }






} 