<?php
/**
 * Created by PhpStorm.
 * User: dacasals
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
use UCI\Boson\IntegratorBundle\ServicesRest\ContentTypeMatcher;


/**
 * Class RestServiceController. Controlador de peticiones a recursos REST.
 *
 * @author Daniel Arturo Casals Amat
 * @package UCI\Boson\IntegratorBundle\Controller
 *
 * @Route("/verb")
 */
class RestServiceController extends Controller
{

    const DOMAIN = 'integrador';

    /**
     * @var array Recibe un arreglo de rutas y propiendades de estas.
     */
    private $rutas;
    use ContentTypeMatcher;

    /**
     * Acción que retorna el listado de servicios y dependencias de la aplicación.
     *
     * @return Response
     */
    public function apiRestAction()
    {
        $server = $this->get('request')->server;

        $servicios = $this->get('integrator.service.rest')->getApi($server);
        return new Response(json_encode($servicios));

    }

    /**
     * Acción que rutea a los servicios que responden a cada petición REST. Emite un objeto response con la respuesta y el código http.
     *
     * @return Response
     */
    public function routerAction()
    {
        $request = $this->get('request');
        $method = $request->getMethod();
        $rutas = Yaml::parse(__DIR__ . '/../Resources/config/rest_global_routing.yml');
        $routeName = $request->get('_route');
        $rooute = $rutas[$routeName];
        $serializer = $this->container->get('jms_serializer');
        $pathInfo = $request->getPathInfo();
        $defaultDomain = $this->container->getParameter('default_domain');

        switch ($method) {
            case 'POST': {
                $arrayResponse = $this->get('integrator.service.verbs.rest')->createAction($request, $rooute);
                break;
            }
            case 'GET': {
                $entidades = $rooute['options']['id'];

                $parts = explode('\\Model\\',$entidades);

                if (count($parts>1)) {

                    $arrayResponse = $this->get('integrator.service.model.rest')->readAction($request, $rooute, $this->container);


                }
                            else{
                                $arrayResponse = $this->get('integrator.service.verbs.rest')->readAction($request, $rooute);
                            }



                break;

            }
            case 'PUT': {
                $arrayResponse = $this->get('integrator.service.verbs.rest')->updateAction($request, $rooute);
                break;
            }
            case 'DELETE': {
                $arrayResponse = $this->get('integrator.service.verbs.rest')->deleteAction($request, $rooute);
                break;
            }
            case 'PATCH': {
                $arrayResponse = $this->get('integrator.service.verbs.rest')->partialUpdateAction();
                break;
            }
            default: {
            $arrayResponse = array(array(), 400);
            break;
            }


        }
        $format = 'json';
        $accepts = $request->headers->get('accept');

        if (strpos($accepts, "*/*")) {
            $format = 'json';
        } else {
            if (strpos($accepts, "application/json")) {
                $format = 'json';
            } elseif (strpos($accepts, "application/xml")) {
                $format = 'xml';
            }
        }
        $objSerialiced = $serializer->serialize($arrayResponse[0], $format);
        $code = $arrayResponse[1];
        $response = new Response($objSerialiced, $code);
        return $response;
    }


}