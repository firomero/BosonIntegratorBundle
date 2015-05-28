<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 27/03/15
 * Time: 8:53
 */

namespace UCI\Boson\IntegratorBundle\Events;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use UCI\Boson\IntegratorBundle\Controller\RestServiceController;
use UCI\Boson\IntegratorBundle\Security\YamlSecurityLoader;
use UCI\Boson\IntegratorBundle\ServicesRest\HttpCode;

/**
 * Listener de los eventos generados en los eventos del proceso de resolucion de dependencias.
 * Class IntegratorListener
 * @package UCI\Boson\IntegratorBundle\Events
 */
class IntegratorListener
{

    /**
     * Verifica que se pueda realizar la petición
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        /*
        * $controller passed can be either a class or a Closure.
        * This is not usual in Symfony but it may happen.
        * If it is a class, it comes in array format
        */
        if (!is_array($controller)) {
            return;
        }

        $token = $event->getRequest()->query->get('token');

        if (current($controller) instanceof RestServiceController) {

               $request = $event->getRequest();
               $etags = $request->getETags();




            if (count($etags)==0) {

                $etags = array();

                if ($request->getMethod()=='POST') {

                    $etags['key'] = $request->attributes->get('key');
                }
                else{

                    $etags['key'] = $request->query->get('key');
                }
            }
               $file = __DIR__.'/../Security/data/appAuth.yml';
               $loader = new YamlSecurityLoader(array('uri'=>$file));
               $configs = $loader->read($file);
               $apps = $configs['application'];

               $keys = array();

               $llave = $etags['key'];
                foreach ($apps as $app) {
                    $keys[]=$app['key'];
                }
                $auth = false;


            foreach ($keys as $single) {
                if ($single==$llave) {
                    $auth = true;
                    break;
                }
                $t = stristr($single,$llave);
                if ($t!==false) {
                    $auth = true;
                    $llave = $single;
                }
            }

            if ($auth===true) {
                $event->getRequest()->attributes->set('auth_token', $llave);
            }
        }


    }

    /**
     * realiza una accion segun el nivel de autorizacion de la respuesta
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
//         check to see if onKernelController marked this as a token "auth'ed" request

        if (!$token = $event->getRequest()->attributes->get('auth_token')) {
            $event->setResponse(new Response('Authentication Required',HttpCode::HTTP_UNAUTHORIZED));
        }

    }
} 