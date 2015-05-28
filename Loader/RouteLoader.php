<?php
/**
 * Created by PhpStorm.
 * User: dacasals
 * Date: 3/02/15
 * Time: 15:25
 */




namespace UCI\Boson\IntegratorBundle\Loader;


use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;
use UCI\Boson\IntegratorBundle\ServicesRest\RestServicesDiscover;


/**
 * Class RouteLoader. Carga las rutas de los recursos REST definidos a la colección de rutas del sistema.
 *
 * @author Daniel Arturo Casals Amat
 * @package UCI\Boson\IntegratorBundle\Loader
 */
class RouteLoader implements LoaderInterface
{

    /**
     * @var bool
     */
    private $loaded = false;
    /**
     * @var RestServicesDiscover
     */
    private $rsdiscover;

    /**
     * Constructor de la clase RestServicesDiscover
     *
     * @param RestServicesDiscover $rsdiscover . Servicio para obtener las rutas detectadas.
     */
    function __construct(RestServicesDiscover $rsdiscover)
    {
        $this->rsdiscover = $rsdiscover;
    }

    /**
     * Funcionalidad para cargar las rutas a la colección de rutas del sistema, estas son escritas en el fichero ../Resources/config/rest_global_routing.yml
     *
     * @param mixed $resource atributos básicos
     * @param null $type
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }
        $routes = $this->rsdiscover->getRutas();
        $rutass = new RouteCollection();
        foreach ($routes as $key => $route) {
            $pattern = $route['pattern'];
            $defaults = array(
                '_controller' => 'IntegratorBundle:RestService:router'
            );
            $requirements = array();
            foreach ($route['options'] as $keyOption => $option) {
                $defaults[$keyOption] = 'all';
                $requirements[$keyOption] = '\d+';
            }

            $Nroute = new Route($pattern, $defaults, $requirements, $route['options'], '', array(), $route['methods']);

            // add the new route to the route collection:
            $routeName = $key;
            $rutass->add($routeName, $Nroute);
        }
        $rep = Yaml::dump($routes);
        file_put_contents(__DIR__ . "/../Resources/config/rest_global_routing.yml", $rep);
        $this->loaded = true;
        return $rutass;
    }

    /**
     * Comprueba que la clase soporte el recurso dado.
     *
     * @param mixed $resource Parametro definido por la interfaz, no utilizado.
     * @param null $type Tipo de recurso o null si no se conoce.
     * @return bool resultado de la comprobación
     */
    public function supports($resource, $type = null)
    {
        return 'rutasRest' === $type;
    }

    /**
     * Método no utilizado  pero necesario para poder implementar la clase LoaderInterface
     */
    public function getResolver()
    {
    }

    /**
     * Método no utilizado  pero necesario para poder implementar la clase LoaderInterface
     *
     * @param LoaderResolverInterface $resolver
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
} 