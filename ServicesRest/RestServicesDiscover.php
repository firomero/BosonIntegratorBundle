<?php
/**
 * Created by PhpStorm.
 * User: root
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
use UCI\Boson\IntegratorBundle\Loader\RouteLoader;

class RestServicesDiscover
{
    private $annotReader;
    private $em;
    private $routeLoader;

    function __construct(AnnotationReader $annotReader, EntityManager $em)
    {
        $this->annotReader = $annotReader;
        $this->em = $em;
    }

    public function getApi(ServerBag $server)
    {
        $AllEntities = $this->em->getMetadataFactory()->getAllMetadata();
        $brindadas = array();
        $necesidas = array();

        foreach ($AllEntities as $entity) {
            $anot = $this->annotReader->getClassAnnotation($entity->getReflectionClass(), 'UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface');
            if ($anot instanceof RestService) {
                $properties = array();
                $id = $entity->identifier;
                $properties[$id[0]] = 'integer';

                foreach ($entity->fieldMappings as $field) {
                    $properties[$field['fieldName']] = $field['type'];
                }
                foreach ($entity->associationMappings as $attrRelaciones) {
                    if ($attrRelaciones['mappedBy'] != null)
                        $properties[$attrRelaciones['fieldName']] = 'array';
                }
                $uri = $server->get("REQUEST_SCHEME") . "://" . $server->get("SERVER_ADDR") . ":" . $server->get("SERVER_PORT") . $server->get("SCRIPT_NAME");

                $arrayNombreAndRuta = $this->obtenerNombreAndRuta($anot,$entity);
                $brindadas[] = array('name' => $arrayNombreAndRuta['routeName'],
                    'allow' => $anot->allow,
                    'version' => $anot->version,
                    'domain' => $anot->domain,
                    'type' => 'servicio',
                    'uri' => $uri.$arrayNombreAndRuta['route'],
                    'properties' => $properties,
                    'bundlename' => $this->obtenerBundleByNamespace($entity->namespace)
                );
            } else if ($anot instanceof RestServiceConsume) {
                $properties = array();
                $id = $entity->identifier;
                $properties[$id[0]] = 'integer';

                foreach ($entity->fieldMappings as $field) {
                    $properties[$field['fieldName']] = $field['type'];
                }
                if ($anot->name == null) {
                    $partes = explode($entity->namespace . "\\", $entity->name);
                    $anot->name = $partes[1];
                }
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

    public function getRutas()
    {

        $AllEntities = $this->em->getMetadataFactory()->getAllMetadata();
        $rutas = array();
        foreach ($AllEntities as $entity) {
            $anot = $this->annotReader->getClassAnnotation($entity->getReflectionClass(), 'UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface');
            if ($anot instanceof RestService) {
                $arrayRutaNameRuta = $this->obtenerNombreAndRuta($anot,$entity);

                $rutas2 =$this->buildRuta($anot,$entity,$rutas,$arrayRutaNameRuta);

                $rutas = array_merge($rutas,$rutas2);
            }
        }
        return $rutas;
    }
    private function buildRuta($anot,ClassMetadata $entity,$rutas,$arrayRutaNameRuta,$RestRouteName = "" , $pattern = "", $method = null,$options = array() ){
        if ($method == null){
            $method = $anot->allow;
        }
        if(count($options) == 0){
            $options = array('id'=>$entity->name);
        }
        $rutas["restRoute_" . $arrayRutaNameRuta['routeName'].$RestRouteName] = array(
            'pattern' => $arrayRutaNameRuta['route'].$pattern,
            'method' => $method,
            'options' => $options
        );
        $arrayRutaNameRuta['routeName'] = $arrayRutaNameRuta['routeName'].$RestRouteName;
        $arrayRutaNameRuta['route'] = $arrayRutaNameRuta['route'].$pattern;

        foreach ($entity->associationMappings as $attrRelaciones) {
            if ($attrRelaciones['mappedBy'] != null) {
                $attr = $this->em->getClassMetadata($attrRelaciones['targetEntity']);
                $anotAttrRelac = $this->annotReader->getClassAnnotation($attr->getReflectionClass(), 'UCI\Boson\IntegratorBundle\Annotation\RestService');

                if ($anotAttrRelac instanceof RestService) {
                   $rutas2= $this->buildRuta(
                                                $anotAttrRelac,
                                                $this->em->getClassMetadata($attrRelaciones['targetEntity']),
                                                $rutas,
                                                $arrayRutaNameRuta,
                                                '_' . $attrRelaciones['fieldName'],
                                                '/' .$attrRelaciones['fieldName'].'/'.'{id'.$attrRelaciones['fieldName'].'}',
                                                $anotAttrRelac->allow,
                                                array('id'=>$entity->name,'id'.$attrRelaciones['fieldName'] =>$attr->name)
                                            );

                    /*$rutas["restRoute_" . $arrayRutaNameRuta['routeName'] . '_' . $attrRelaciones['fieldName']] = array(
                        'pattern' => $arrayRutaNameRuta['route'] . '/' .$attrRelaciones['fieldName'].'/'.'{id'.$attrRelaciones['fieldName'].'}',
                        'method' => $anotAttrRelac->allow,
                        'options'=> array('id'=>$entity->name,'id'.$attrRelaciones['fieldName'] =>$attr->name)
                    );*/
                    $rutas = array_merge($rutas,$rutas2);
                }
            }
        }
        return $rutas;
    }

    private function obtenerNombreAndRuta(RestService $anot,ClassMetadata $entity){
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
        $ruta = $ruta . '/'.'{id}';
        return array('routeName'=>$nameRoute,'route' =>$ruta);
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