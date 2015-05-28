<?php


namespace UCI\Boson\IntegratorBundle\Manager;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\Translator;
use UCI\Boson\IntegratorBundle\Events\ClientEvents;
use UCI\Boson\IntegratorBundle\Events\GetClientEvents;
use UCI\Boson\IntegratorBundle\Events\RetrieveSuscriber;
use UCI\Boson\IntegratorBundle\Exception\IntegratorException;

/**
 * Fachada
 * Class IntegratorKernel
 * @package IntegratorBundle\Manager
 */
class IntegratorKernel {

    const RESOURCE_SERVICE = 'services';
    const RESOURCE_DEPENDENCY = 'dependency';

    /**
     * @var mixed $config
     */
    protected $config;


    /**
     *@var ServiceManager $serviceManager
     * */
    protected $serviceManager;


    protected $eventDispatcher;


    /**
     * @var array
     */
    protected $recursos;

    /**
     * @var Translator
     */
    private  $translator;

    /**
     * @return array
     */
    public function getRecursos()
    {
        return $this->recursos;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }


    protected $logger ;

    /**
     * @param $server
     * @param $client
     */
    public function __construct($server, $client,ServiceManager $serviceManager, EventDispatcherInterface $dispatcher, Logger $logger,Translator $translator )
    {

        $this->config['server']=$server;

        $this->config['client']=$client;

        $this->serviceManager = $serviceManager;

        $this->serviceManager->setEventDispatcher($dispatcher);

        $this->eventDispatcher = $dispatcher;

        $suscriber = new RetrieveSuscriber();

        $this->eventDispatcher->addSubscriber($suscriber);

        $this->logger = $logger;

        $this->translator = $translator;
        try{

            $this->recursos = $this->CargarRecursos();
        }
        catch(\Exception $e)
        {
            $this->logger->addCritical($e->getMessage());
        }



    }


    /**
     * Cargar los recursos registrados en el ecosistema definido
     * @return array
     */
    public function CargarRecursos()
    {
        $is_server = $this->config['server']['is_server'];
        $recursos = array();
        $flatten = array(

        );
        $services = array();
        $dependency = array();
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
        foreach ($recursos as $recurso) {
            $services = array_merge($services,$recurso[self::RESOURCE_SERVICE] );
            $dependency = array_merge($dependency,$recurso[self::RESOURCE_DEPENDENCY] );

        }

        $flatten = array_merge($dependency,$services);

        return $flatten;
    }

    /**
     *Genera el mapa de dependencias
     */
    public function buildMap()
    {

        $this->serviceManager->buildDependecyGraph($this->recursos==null?array():$this->recursos);
    }

    /**
     * Recupera un servicio.
     * @param $domain
     * @param $name
     * @return mixed|string
     * @throws IntegratorException
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
     * @throws \Exception
     * @return mixed
     */
    public function Representaciones($url)
    {
        $client = new Client();
        try{

            /**
            * @var Response $response
             */
            $response = $client->get($url);

            if ($response->getStatusCode()==200) {
                $json = $response->json();

                return $json;
            }

        }

        catch(\Exception $e)
        {

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Retorna la url del servicio solicitado
     * @param $dependencia
     * @return mixed|string
     * @throws \Exception
     */
    public function getURI($dependencia)
    {

       $dependencia[ServiceManager::INDEX_SEARCH_TYPE]='dependencia';
        $clientEvents = new GetClientEvents($this);
        $this->eventDispatcher->dispatch(ClientEvents::PRE_RETRIEVE,$clientEvents);
        $uri =  $this->serviceManager->getServicio($dependencia);
        $this->eventDispatcher->dispatch(ClientEvents::POST_RETRIEVE,$clientEvents);
        return $uri;
    }


    /**
     * Retorna si el service manager está vacío
     * @return bool
     */
    public function isNew()
    {
        return $this->serviceManager->isNew();
    }






} 