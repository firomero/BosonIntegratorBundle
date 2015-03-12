<?php


namespace IntegratorBundle\Manager;

use GuzzleHttp\Client;

class IntegratorKernel {

    /**
     * @var mixed $config
     */
    protected $config;


    /**
     *@var ServiceManager $serviceManager
     * */
    protected $serviceManager;

    /**
     * @param $server
     * @param $client
     */
    public function __construct($server, $client, $serviceManager)
    {

        $this->config['server']=$server;
        $this->config['client']=$client;
        $this->serviceManager = $serviceManager;
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
            foreach ($applist as $app) {
                array_push($recursos, $this->Representaciones($app));
            }

        }

        return $recursos;
    }

    /**
     * Obtiene los servicios y dependendencias de una aplicacion
     * @param $url
     * @return mixed
     */
    public function Representaciones($url)
    {
        $client = new Client();
        $response = $client->get($url);
        $json = $response->json();

        return $json;


    }






} 