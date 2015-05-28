<?php
/**
 * Created by PhpStorm.
 * User: dacasals
 * Date: 31/01/15
 * Time: 14:24
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\HttpFoundation\ServerBag;
use UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface;
use UCI\Boson\IntegratorBundle\Annotation\RestService;
use UCI\Boson\IntegratorBundle\Annotation\RestServiceConsume;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RestServicesDiscover Servicio encargado de descubrir los recursos Rest expuestos en los componentes y preparar el sistema para su consumo
 *
 * @author Daniel Arturo Casals Amat
 * @package UCI\Boson\IntegratorBundle\ServicesRest
 */
class RestServicesDiscover
{
    /**
     * @var AnnotationReader
     */
    private $annotReader;
    /**
     * @var EntityManager
     */
    private $em;

    use UtilRestDiscover;

    /**
     * Construnctor de la clase
     *
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->annotReader = $container->get('annotations.reader');
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->container = $container;

    }

    /**
     * Funcionalidad encargada de generar el api de servicios y dependencias para el consumo del resolver y de cualquier sistema que desee obtener los servicios y dependencias del sistema.
     *
     * @param ServerBag $server Parametros del request  que posee información del servidor
     * @return array Retorna un arreglo con los servicios y dependencias de la siguiente forma: {service: array, dependency: array}
     */
    public function getApi(ServerBag $server)
    {
        $AllMetadataResources = $this->fetchMetadata();

        $brindadas = array();
        $necesidas = array();
        /* Recorriendo arreglo de clases de Metadatos  de los servicios */
        foreach ($AllMetadataResources as $entity) {
            $anot = $this->annotReader->getClassAnnotation($entity->getReflectionClass(), 'UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface');
            /*  si es un recurso etiquetado como servicio */
            if ($anot instanceof RestService) {
                $properties = array();
                $id = $entity->identifier;

            /* obteniendo párametro identificador */
                if (isset($id[0])) {
                    $properties[$id[0]] = 'integer';
                }

                /* obteniendo el resto de los párametros del recurso */
                foreach ($entity->fieldMappings as $field) {
                    $properties[$field['fieldName']] = $field['type'];
                }
                foreach ($entity->associationMappings as $attrRelaciones) {
                    if ($attrRelaciones['mappedBy'] != null)
                        $properties[$attrRelaciones['fieldName']] = 'array';
                }
                /* generando url del recurso */
                $uri = $server->get("REQUEST_SCHEME") . "://" . $server->get("SERVER_ADDR") . ":" . $server->get("SERVER_PORT") . $server->get("SCRIPT_NAME");

                $arrayNombreAndRuta = $this->obtenerNombreAndRuta($anot, $entity);
                /* obteniendo el resto de información del recurso */
                $brindadas[] = array('name' => $arrayNombreAndRuta['routeName'],
                    'allow' => $anot->allow,
                    'version' => $anot->version,
                    'domain' => $anot->domain,
                    'type' => 'servicio',
                    'uri' => $uri . $arrayNombreAndRuta['route'],
                    'properties' => $properties,
                    'bundlename' => $this->obtenerBundleByNamespace($entity->namespace)
                );
            }

            /*  si es un recurso etiquetado como dependencia */
            else if ($anot instanceof RestServiceConsume) {
                $properties = array();
                $id = $entity->identifier;

                /* obteniendo párametro identificador */
                if (isset($id[0])) {
                    $properties[$id[0]] = 'integer';
                }

                /* obteniendo el resto de los párametros o campos del recurso */
                foreach ($entity->fieldMappings as $field) {
                    $properties[$field['fieldName']] = $field['type'];
                }
                if ($anot->name == null) {
                    $partes = explode($entity->namespace . "\\", $entity->name);
                    $anot->name = $partes[1];
                }

                /* obteniendo el resto de información del recurso */
                $necesidas[] = array('name' => $anot->name,
                    'optional' => $anot->getOptional(),
                    'version' => $anot->version,
                    'domain' => $anot->domain,
                    'type' => 'dependencia',
                    'properties' => $properties,
                    'bundlename' => $this->obtenerBundleByNamespace($entity->namespace)
                );
            }
        }
        return array('services' => $brindadas, 'dependency' => $necesidas);

    }

    /**
     * Devuelve el listado de rutas de los recursos REST, tanto los relacionadas con entidades como los personalizados.
     *
     * @return array Retorna el arreglo de rutas
     */
    public function getRutas()
    {
//        $namespaces = $this->getModels($this->container);
//        $restEntities = $this->getRestMetadata($namespaces);
        $AllEntities = $this->fetchMetadata();

        $rutas = array();
        foreach ($AllEntities as $entity) {
            $anot = $this->annotReader->getClassAnnotation($entity->getReflectionClass(), 'UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface');
            if ($anot instanceof RestService) {
                $arrayRutaNameRuta = $this->obtenerNombreAndRuta($anot, $entity);

                $rutas2 = $this->buildRuta($anot, $entity, $rutas, $arrayRutaNameRuta);

                $rutas = array_merge($rutas, $rutas2);
            }
        }
        return $rutas;
    }

    /**
     * Agrega el resto de recurso etiquetados como servicios que no se corresponden a entidades, sino a recurso personalizados a la lista del api.
     * @return array Arreglo de recursos personalizados
     */
    protected function fetchMetadata()
    {
        $AllEntities = $this->em->getMetadataFactory()->getAllMetadata();
        $namespaces = $this->getModels($this->container);
        $restEntities = $this->getRestMetadata($namespaces);

        $AllEntities = array_merge($restEntities, $AllEntities);

        return $AllEntities;
    }

    /**
     * Método recursivo para construir las rutas de forma tal que puedan ser agragadas al RouteCollection
     *
     * @param ServiceAnnotationInterface $anot  Anotación de servicios.
     * @param ClassMetadata $entity Clase de metadatos de Doctrine de la entidad a convertir en recurso
     * @param array $rutas  Arreglo de rutas generadas antes de la llamada recursiva
     * @param array $arrayRutaNameRuta  Arreglo de  nombre de ruta y la url del recurso
     * @param string $RestRouteName Nombre del recurso para la ruta.
     * @param string $pattern patrón de la ruta , ver definición de rutas, atributo pattern
     * @param null $method Métodos permitidos para esta ruta Ejemplo GET,POST,PUT,DELETE...
     * @param array $options Propiedades extras
     * @return array retorna el arreglo de rutas conformadas para subirlos con un loader
     */
    private function buildRuta($anot, ClassMetadata $entity, $rutas, $arrayRutaNameRuta, $RestRouteName = "", $pattern = "", $method = null, $options = array())
    {

        if ($method == null) {
            $method = $anot->allow;
        }
        if (count($options) == 0) {
            $options = array('id' => $entity->name);
        }
        $rutas["restRoute_" . $arrayRutaNameRuta['routeName'] . $RestRouteName] = array(
            'pattern' => $arrayRutaNameRuta['route'] . $pattern,
            'methods' => $method,
            'options' => $options,
            'properties' => $this->getEntityProperties($entity, $anot)
        );
        $arrayRutaNameRuta['routeName'] = $arrayRutaNameRuta['routeName'] . $RestRouteName;
        $arrayRutaNameRuta['route'] = $arrayRutaNameRuta['route'] . $pattern;

        foreach ($entity->associationMappings as $attrRelaciones) {
            if ($attrRelaciones['mappedBy'] != null) {
                $attr = $this->em->getClassMetadata($attrRelaciones['targetEntity']);
                $anotAttrRelac = $this->annotReader->getClassAnnotation($attr->getReflectionClass(), 'UCI\Boson\IntegratorBundle\Annotation\RestService');

                if ($anotAttrRelac instanceof RestService) {
                    $options['id' . $attrRelaciones['fieldName']] = $attr->name;
                    $properties = $this->getEntityProperties($attr, $anotAttrRelac);
                    $rutas2 = $this->buildRuta(
                        $anotAttrRelac,
                        $this->em->getClassMetadata($attrRelaciones['targetEntity']),
                        $rutas,
                        $arrayRutaNameRuta,
                        '_' . $attrRelaciones['fieldName'],
                        '/' . $attrRelaciones['fieldName'] . '/' . '{id' . $attrRelaciones['fieldName'] . '}',
                        $anotAttrRelac->allow,
                        $options,
                        $properties
                    );
                    $rutas = array_merge($rutas, $rutas2);
                }
            }
        }
        return $rutas;
    }

    /**
     * Funcionalidad que permite obtener las propiedades de un recurso, solo las nativas de la entidad, no se obtienen los campos de las relaciones inversas.
     *
     * @param ClassMetadata $entity Clase de metadatos de Doctrine correspondiente al recurso para obenter sus .
     * @param ServiceAnnotationInterface $AnnotClassReference Interfaz para verificar si es un recurso REST
     * @return array Arreglo de proiendades del recurso
     */
    private function getEntityProperties(ClassMetadata $entity, ServiceAnnotationInterface $AnnotClassReference)
    {
        $properties = array();
        $anot = $this->annotReader->getClassAnnotation($entity->getReflectionClass(), $AnnotClassReference);
        if ($anot instanceof RestService) {
            $properties = array();
            $id = $entity->identifier;

            if (isset($id[0])) {
                $properties[$id[0]] = 'integer';
            }

            foreach ($entity->fieldMappings as $field) {
                $properties[$field['fieldName']] = $field['type'];
            }
        }
        return $properties;
    }

    /**
     * Devuelve el nombre y la ruta para un recurso.
     *
     * @param RestService $anot Anotación de recursos REST
     * @param ClassMetadata $entity Clase de metadatos de Doctrine.
     * @return array Retorna el arreglo con el nombre y la ruta,
     */
    private function obtenerNombreAndRuta(RestService $anot, ClassMetadata $entity)
    {
        $ruta = "/api";
        if ($anot->domain != null) {
            $ruta = $ruta . "/" . $anot->domain;
        }
        $nameRoute = "";
        if ($anot->name != null) {
            $ruta = $ruta . "/" . $anot->name;
            $nameRoute = $anot->name;
        } else {
            /*obtener nombre entidad*/
            $partes = explode($entity->namespace . "\\", $entity->name);
            $ruta = $ruta . "/" . strtolower($partes[1]);
            $nameRoute = strtolower($partes[1]);
        }
        $ruta = $ruta . '/' . '{id}';
        return array('routeName' => $nameRoute, 'route' => $ruta);
    }

    /**
     * Obtener el nombre del bundle dado un namespace.
     *
     * @param string $namespace
     * @return string Retorna el nombre del bundle.
     */
    private function obtenerBundleByNamespace($namespace)
    {
        $arrayRutaFile = explode("\\", $namespace);
        $conteopartesRuta = count($arrayRutaFile);
        for ($i = $conteopartesRuta - 1; $i >= 0; $i--) {
            if (preg_match('/Bundle$/', $arrayRutaFile[$i]) == 1 && $arrayRutaFile[$i + 1] == "Entity") {
                return $arrayRutaFile[$i];
            }
        }
    }
} 