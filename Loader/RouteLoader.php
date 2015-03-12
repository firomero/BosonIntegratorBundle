<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/02/15
 * Time: 15:25
 */

namespace UCI\Boson\IntegratorBundle\Loader;


use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;
use UCI\Boson\IntegratorBundle\ServicesRest\RestServicesDiscover;


/**
 * Class RouteLoader
 *
 * @author Daniel Arturo Casals Amat
 * @package UCI\Boson\IntegratorBundle\Loader
 */
class RouteLoader implements  LoaderInterface{

    /**
     * @var bool
     */
    private $loaded = false;
    /**
     * @var RestServicesDiscover
     */
    private  $rsdiscover ;

    /**
     * @param RestServicesDiscover $rsdiscover
     */
    function __construct(RestServicesDiscover $rsdiscover)
    {
        $this->rsdiscover = $rsdiscover;

    }

    /**
     * @param mixed $resource
     * @param null $type
     * @return RouteCollection
     */
    public function load( $resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }
        $routes = $this->rsdiscover->getRutas();
        $rutass = new RouteCollection();
        foreach($routes as $key =>$route){
            $pattern = $route['pattern'];
            $defaults = array(
                '_controller' => 'IntegratorBundle:RestService:router'
            );
            $requirements = array();
            foreach($route['options'] as $keyOption =>$option ){
                $defaults[$keyOption] = 'all';
                $requirements[$keyOption] =  '\d+';
            }

            $Nroute = new Route($pattern, $defaults,$requirements,$route['options'],'',array(),$route['method']);
            // add the new route to the route collection:
            $routeName = $key;
            $rutass->add($routeName, $Nroute);

        }
        $rep = Yaml::dump($routes);
        file_put_contents(__DIR__."/../Resources/config/rest_global_routing.yml",$rep);
        $this->loaded = true;
        return $rutass;
    }

    /**
     * @param mixed $resource
     * @param null $type
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'rutasRest' === $type;
    }

    /**
     *
     */
    public function getResolver()
    {
    }

    /**
     * @param LoaderResolverInterface $resolver
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }

} 