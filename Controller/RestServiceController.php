<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 31/01/15
 * Time: 14:25
 */

namespace UCI\Boson\IntegratorBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use UCI\Boson\ExcepcionesBundle\Exception\LocalException;

/**
 * @Route("/verb")
 */
class RestServiceController extends Controller
{

    private $rutas;

    public function apiRestAction()
    {
        //throw new LocalException('E1');
        $server = $this->get('request')->server;
        $servicios = $this->get('integrator.service.rest')->getApi($server);

        return new Response(json_encode($servicios));

    }
    public function routerAction(){
        if(!isset($this->rutas)){
            $this->rutas = $this->container->get('integrator.service.rest')->getRutas();
        }
        $request = $this->get('request');

        $method = $request->getMethod();
        $rutas = Yaml::parse(__DIR__.'/../Resources/config/rest_global_routing.yml');
        $routeName =$request->get('_route');
        $rooute = $rutas[$routeName];

        switch($method){
            case 'POST':{

                return $this->get('integrator.service.verbs.rest')->createAction($request,$rooute);

            }
            case 'GET': {
                $serializer = $this->container->get('jms_serializer');

                $objects = $this->get('integrator.service.verbs.rest')->readAction($request,$rooute);
                $json = $serializer->serialize($objects,'json');
                            return new Response($json);
            }
            case 'PUT': {
                return $this->get('integrator.service.verbs.rest')->updateAction();
            }
            case 'DELETE':{
                return $this->get('integrator.service.verbs.rest')->deleteAction();
            }
            case 'PATCH': {
                return $this->get('integrator.service.verbs.rest')->partialUpdateAction();
            }
        }
        //return new Response($this->getRequest()->getMethod(). $this->getRequest()->getRequestUri());
    }




} 