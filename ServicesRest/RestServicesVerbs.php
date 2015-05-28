<?php
/**
 * Created by PhpStorm.
 * User: dacasals
 * Date: 31/01/15
 * Time: 14:24
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Exception\FatalErrorException;
use UCI\Boson\IntegratorBundle\Annotation\RestService;


/**
 * Class RestServicesVerbs
 *
 * @author Daniel Arturo Casals Amat
 * @package UCI\Boson\IntegratorBundle\ServicesRest
 */
class RestServicesVerbs
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var Serializer
     */
    private $jms;
    /**
     * @var Translator
     */
    private $trans;
    /**
     * @var AnnotationReader
     */
    private $annotationReader;
    /**
     * @var RestServicesDiscover
     */
    private $restServiceDiscover;

    /**
     * Constructor de la clase
     *
     * @param EntityManager $em
     * @param Serializer $jms
     * @param Translator $trans
     * @param AnnotationReader $annotationReader
     * @param RestServicesDiscover $restServicesDiscover
     */
    function __construct(EntityManager $em, Serializer $jms, Translator $trans, AnnotationReader $annotationReader, RestServicesDiscover $restServicesDiscover)
    {
        $this->em = $em;
        $this->jms = $jms;
        $this->trans = $trans;
        $this->annotationReader = $annotationReader;
        $this->restServiceDiscover = $restServicesDiscover;
    }

    /**
     * Funcionalidad que responde a peticiones de lectura (GET)
     *
     * @param Request $request Objeto request de la petición recibida.
     * @param array $route Ruta de la petición en formato de arreglo que fue recibida, esta contiene información relevante para la funcionalidad
     * @return array Retorna un arreglo de la forma array(arreglo de objetos, codigo http)
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function readAction(Request $request, $route)
    {
        $entidades = $route['options'];
        $properties = $route['properties'];
        $entidad = $entidades['id'];
        $cm = $this->em->getClassMetadata($entidad);
        $cmMuchos = $this->em->getClassMetadata($entidad);
        $qb = $this->em->createQueryBuilder();
        $qb->select('a')->from($entidad, 'a');

        try {

            if ($request->get('id') != 'all') {
                $qb->where('a.' . $cm->getSingleIdentifierFieldName() . ' = :identificator')
                    ->setParameter('identificator', $request->get('id'));
                $aliasCurrent = 'a';
                $resto = array_splice($entidades, 0, 1);
                $esUnAll = false;
                $getArrayMethods = array();
                foreach ($entidades as $key => $entidad) {
                    $qb->innerJoin($aliasCurrent . '.' . substr($key, 2), $key);
                    if ($request->get($key) != 'all') {
                        $cmMuchos = $this->em->getClassMetadata($entidad);
                        $qb->andWhere($key . '.' . $cmMuchos->getSingleIdentifierFieldName() . ' = :identificatorMuchos' . $key)
                            ->setParameter('identificatorMuchos' . $key, $request->get($key));
                        $aliasCurrent = $key;
                        $qb->addSelect(array($aliasCurrent));
                        array_push($getArrayMethods, substr($aliasCurrent, 2));
                    } else {
                        $cmMuchos = $this->em->getClassMetadata($entidad);
                        $aliasCurrent = $key;
                        $qb->addSelect(array($aliasCurrent));
                        $esUnAll = true;
                        array_push($getArrayMethods, substr($aliasCurrent, 2));
                        break;
                    }
                }
                $this->applyGetParameters($request, $qb, $aliasCurrent, $esUnAll, $properties);
                if ($esUnAll) {
                    /* agragando hypermedia a la consulta */
                    $arrayHypermedia = $this->addHypermedia($request, $cmMuchos, $qb, $aliasCurrent);
                    $qb = $arrayHypermedia['qb'];
                    $objeto = $qb->getQuery()->getArrayResult();
                    if (count($objeto) == 0) {
                        return array($objeto, 200);
                    } else {
                        $respuesta = $this->getLastByArrayIndex($objeto[0], $getArrayMethods);
                        $respuesta = $this->setHypermToArray($respuesta, $arrayHypermedia['hypermedia']);
                        return array($respuesta, 200);
                    }
                } /* Si la petición es para un recurso específico dado el identidicador */
                else {
                    $arrayHypermedia = $this->addHypermedia($request, $cmMuchos, $qb, $aliasCurrent);
                    $qb = $arrayHypermedia['qb'];
                    $arrayObjects = $qb->getQuery()->getArrayResult();
                    if (count($arrayObjects) == 0) {
                        /* Retornar que no se encontro*/
                        $messageResponse = $this->trans->trans('custom_messages.404GET', array(), 'http_codes');
                        return array($messageResponse, 404);
                    }
                    /* si la ruta petición es para un ruta directa al recurso, Ej api/dominio/recursoName/{id} */
                    if ($aliasCurrent == 'a') {
                        $respuesta = $this->setHypermToArray($arrayObjects, $arrayHypermedia['hypermedia']);
                        return array($respuesta[0], 200);
                    } else {
                        $respuesta = $this->getLastByArrayIndex($arrayObjects[0], $getArrayMethods);
                        $respuesta = $this->setHypermToArray($respuesta, $arrayHypermedia['hypermedia']);
                        return array($respuesta, 200);
                    }
                }
            } /* Si no es un obtener todos*/
            else {
                $this->applyGetParameters($request, $qb, 'a', true, $properties);
                $arrayHypermedia = $this->addHypermedia($request, $cm, $qb, 'a');
                $qb = $arrayHypermedia['qb'];

                $arrayObjects = $qb->getQuery()->getArrayResult();
                $arrayObjects = $this->setHypermToArray($arrayObjects, $arrayHypermedia['hypermedia']);
                return array($arrayObjects, 200);
            }

        } catch (\PDOException $ex) {
            $errorsplited = explode('FATAL:  database', $ex->getMessage());
            if (count($errorsplited) > 1) {
                return array($this->trans->trans('custom_messages.500DBDownPOST', array(), 'http_codes'), 500);
            }
        } catch (DBALException $ex) {
            $errorsplited = explode('Invalid datetime format:', $ex->getMessage());
            if (count($errorsplited) > 1) {
                return array($this->trans->trans('custom_messages.400GETDate', array(), 'http_codes'), 400);
            } elseif (count($errorsplited = explode('invalid input syntax for integer:', $ex->getMessage())) > 1) {
                return array(sprintf($this->trans->trans('custom_messages.400GETInteger', array(), 'http_codes'), $errorsplited[1]), 400);
            } else {
                return array($ex->getMessage(), $ex->getCode());
            }
        }
    }

    /*
     * Este método setea los valores a ser devueltos según  el tipo de hipermedia seleccionado(hypermedia,object,identifier)
     */
    /**
     * @param $values
     * @param $arrayHypermedia
     * @return mixed
     */
    private function setHypermToArray($values, $arrayHypermedia)
    {

        foreach ($arrayHypermedia as $field) {
            if (array_key_exists('object', $field))
                return $values;
            elseif (array_key_exists('uri', $field)) {
                $countI = count($values);
                for ($i = 0; $i < $countI; $i++) {
                    $countJ = count($values[$i][$field['name']]);
                    for ($j = 0; $j < $countJ; $j++) {
                        $values[$i][$field['name']][$j] = $field['uri'] . '/' . $values[$i][$field['name']][$j][$field['identificatorField']];
                    }
                }
            } elseif (array_key_exists('identifier', $field)) {
                $countI = count($values);
                for ($i = 0; $i < $countI; $i++) {
                    $countJ = count($values[$i][$field['name']]);
                    for ($j = 0; $j < $countJ; $j++) {
                        $values[$i][$field['name']][$j] = $values[$i][$field['name']][$j][$field['identificatorField']];
                    }
                }
            }
            return $values;

        }
        return $values;

    }

    /**
     * Esta funcionalidad agrega hypermedia a la consulta utilizada para responder a la petición, según el tipo de hypermedia con que se etiqueto el campo con la anotación RestRelationField
     *
     * @param Request $request Objeto Request de la petición recibida
     * @param ClassMetadata $entity Clase de metadatos de la entidad a revisar
     * @param QueryBuilder $qb Consulta a modificar agregandole los valores de hypermedia
     * @param $aliasCurrent Alias actual de la entidad en la consulta
     * @return array Retorna un arreglo con  array('qb' => $qb, 'hypermedia' => $respuesta) con el objeto consulta modificado y la
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    private function addHypermedia(Request $request, ClassMetadata $entity, QueryBuilder $qb, $aliasCurrent)
    {
        $relaciones = $entity->getAssociationMappings();
        $respuesta = array();
        foreach ($relaciones as $itemRel) {
            if ($itemRel['mappedBy'] != null) {
                $attr = $this->em->getClassMetadata($itemRel['targetEntity']);
                $anotAttrRelac = $this->annotationReader->getClassAnnotation($attr->getReflectionClass(), 'UCI\Boson\IntegratorBundle\Annotation\RestService');

                $anotField = $this->annotationReader->getPropertyAnnotation($entity->getReflectionProperty($itemRel['fieldName']), 'UCI\Boson\IntegratorBundle\Annotation\RestRelationField');
                if ($anotField != null) {
                    if ($anotField->type == "HYPERMEDIA") {
                        $server = $request->server;
                        $uri = $server->get("REQUEST_SCHEME") . "://" . $server->get("SERVER_ADDR") . ":" . $server->get("SERVER_PORT") . $server->get("SCRIPT_NAME");
                        $ruta = $this->obtenerRuta($anotAttrRelac, $attr);
                        array_push($respuesta, array('name' => $itemRel['fieldName'], 'uri' => $uri . $ruta, 'identificatorField' => $attr->getSingleIdentifierFieldName()));
                    } elseif ($anotField->type == "IDENTIFIER") {
                        array_push($respuesta, array('name' => $itemRel['fieldName'], 'identifier' => true, 'identificatorField' => $attr->getSingleIdentifierFieldName()));

                    } elseif ($anotField->type == "OBJECT") {
                        array_push($respuesta, array('name' => $itemRel['fieldName'], 'object' => true));
                    }
                    $qb->leftJoin($aliasCurrent . '.' . $itemRel['fieldName'], 'rel' . $itemRel['fieldName']);
                    $qb->addSelect('rel' . $itemRel['fieldName']);
                }
            }
        }
        return array('qb' => $qb, 'hypermedia' => $respuesta);
    }

    /**
     * Funcionalidad para obtener el arreglo de valores que va a ser devueltos, la consulta realizada utiliza puede incluir información que no debe ser devuelta solo la iformacion del recurso solicitado es devuelta al usuario según la petición
     * @param $objeto
     * @param $getArrayMethods
     * @return mixed
     */
    private function getLastByArrayIndex($objeto, $getArrayMethods)
    {
        $respuesta = $objeto;
        $length = count($getArrayMethods);
        for ($i = 0; $i < $length; $i++) {
            $respuesta = $respuesta[$getArrayMethods[$i]];
            if ($i == $length - 1) {
                break;
            } else {
                $respuesta = $respuesta[0];
            }
        }
        return $respuesta;
    }

    /**
     * Funcionalidad que permite aplicar parámetros de búsqueda, ordenamiento y paginación a la cosulta
     *
     * @param Request $request Objeto Request de la petición recibida
     * @param QueryBuilder $qb Consulta para obtener los datos según la petición
     * @param string $alias Alias actual de la entidad de la consulta
     * @param bool $esUnAll Booleano que especifica si es un obtener todos o si se requirio un recurso mediante un identidicador
     * @param $properties Propiedades o atributo de la entidad por los que se va a permitir búsqueda.
     */
    private function applyGetParameters(Request $request, QueryBuilder $qb, $alias = 'a', $esUnAll = false, $properties)
    {
        $parameters = $request->query->all();
        if ($alias == 'a' && $esUnAll) {
            foreach ($parameters as $key => $param) {
                if (preg_match('/^sortby/', strtolower($key))) {
                    $this->addOrderBy($qb, str_replace('sortBy', "", $key), $param, $alias);
                } elseif ('start' == $key) {
                    $this->addStartParam($qb, $param);
                } elseif ('limit' == $key) {
                    $this->addLimitParam($qb, $param);
                } elseif (array_key_exists($key, $properties)) {
                    $this->addSearchParam($key, $param, $qb, $alias, $esUnAll, $properties);
                }
            }
        }

    }

    /**
     * Agrega ordenamiento a la consulta para una propiedad.
     *
     * @param QueryBuilder $qb Consulta a modificar
     * @param $key atributo por el que se va a ordenar
     * @param $param Tipo de ordenamiento (ASC o DESC)
     * @param $alias Alias de la entidad para uso de la consulta
     */
    private function  addOrderBy(QueryBuilder $qb, $key, $param, $alias)
    {
        if (strtolower($param) == 'asc' || strtolower($param) == 'desc') {
            $qb->addOrderBy($alias . '.' . $key, $param);
        }
    }

    /**
     * Agrega parámetros de búsqueda a la consulta
     *
     * @param string $key Atributo o campo por el que se va  buscar
     * @param $param Valor de búsqueda por el campo $key especificado
     * @param QueryBuilder $qb Consulta que se va a modificar con los parámetros de búsqueda
     * @param string $alias Alias de la entidad para uso de la consulta
     * @param bool $esUnAll Booleano que especifica si es un botener todos
     * @param array $properties Arreglo de propiedades para obtener el tipo de dato del párametro
     */
    private function addSearchParam($key, $param, QueryBuilder $qb, $alias = 'a', $esUnAll = false, $properties)
    {
        $DQLWere = $qb->getDQLPart('where');
        if ($properties[$key] == "string") {
            if ($DQLWere == null && count($DQLWere) == 0) {
                $qb->where('LOWER(' . $alias . '.' . $key . ')  LIKE :' . $key . 'searchparameter')
                    ->setParameter($key . 'searchparameter', '%' . strtolower($param) . '%');
            } else {
                $qb->andWhere('LOWER(' . $alias . '.' . $key . ')  LIKE :' . $key . 'searchparameter')
                    ->setParameter($key . 'searchparameter', '%' . strtolower($param) . '%');
            }
        } else {

            if ($DQLWere == null && count($DQLWere) == 0) {
                $qb->where($alias . '.' . $key . ' = :' . $key . 'searchparameter')
                    ->setParameter($key . 'searchparameter', $param);
            } else {
                $qb->andWhere($alias . '.' . $key . '  = :' . $key . 'searchparameter')
                    ->setParameter($key . 'searchparameter', $param);
            }
        }
    }

    /**
     * Agrega el párametro start de para la paginación
     * @param QueryBuilder $qb Consulta a ser modificada.
     * @param $starParam Valor del párametro limit
     */
    private function addStartParam($qb, $starParam)
    {
        if ($starParam != '' && is_numeric($starParam)) {
            $qb->setFirstResult($starParam);
        }
    }

    /**
     * Agrega el párametro limit de para la paginación
     *
     * @param $qb Consulta a ser modificada.
     * @param $limitParam  Valor del párametro limit
     */
    private function addLimitParam($qb, $limitParam)
    {
        if ($limitParam != '' && is_numeric($limitParam)) {
            $qb->setMaxResults($limitParam);
        }
    }

    /**
     * Funcionalidad que permite atender la peticiones de creacion de objetos (POST)
     *
     * @param Request $request Objeto Request de la petición
     * @param $route Arreglo de valores asociados a la ruta accedida
     * @return array Arreglo de valors en el formato array(mensaje de respuesta,codigo http de la respuesta)
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function createAction(Request $request, $route)
    {
        $parameters = $request->request->all();

        $entidades = $route['options'];

        $entidad = array_pop($entidades);
        $cmEntidad = $this->em->getClassMetadata($entidad);
        $columNameIdentifier = $cmEntidad->getSingleIdentifierFieldName();
        $object = new $entidad;
        $fielFields = $cmEntidad->reflFields;
        $fieldRelations = $cmEntidad->associationMappings;
        $fieldMappings = $cmEntidad->fieldMappings;
        foreach ($parameters as $key => $field) {
            if ($key == $columNameIdentifier) {
                continue;
            }
            /*pregunto si esta entre los reflected fields de la clase*/
            if ($val = array_key_exists($key, $fielFields) !== false) {
                /*ahora pregunto si se encuentra entre los campos convencionales de la entidad*/
                if (array_key_exists($key, $fieldMappings) !== false) {
                    $method = 'set' . ucwords($key);
                    $object->$method($field);
                } /*si no pregunto si esta entre los campos de relaciones */
                else if (array_key_exists($key, $fieldRelations) !== false && $fieldRelations[$key]['mappedBy'] === null) {
                    $method = 'set' . ucwords($key);
                    $fieldMetadata = $fieldRelations[$key];
                    try {
                        $parent = $this->em->getRepository($fieldMetadata['targetEntity'])->find($field);
                    } catch (\PDOException $ex) {
                        $errorsplited = explode('FATAL:  database', $ex->getMessage());
                        if (count($errorsplited) > 0) {
                            return array($this->trans->trans('custom_messages.500DBDownPOST', array(), 'http_codes'), 500);
                        }
                    }

                    if ($parent != null) {
                        $object->$method($parent);
                    }
                }
            }
        }
        try {

            $this->em->persist($object);
            $this->em->flush();

        } catch (DBALException $ex) {
            $errorsplited = explode('Not null violation', $ex->getMessage());
            if (count($errorsplited) > 0) {

                $errorsplited = explode('"', $errorsplited[1]);
            }
            return array(sprintf($this->trans->trans('custom_messages.500NOTNULFieldPOST', array(), 'http_codes'), $errorsplited[1]), 500);
        } catch (\Exception $ex) {
            return array($ex->getMessage(), 500);
        }
        $messageResponse = $this->trans->trans('custom_messages.201POST', array(), 'http_codes');
        return array($messageResponse, 201);
    }

    /**
     * Funcionalidad que permite atender la petición de modificación de un recurso (PUT)
     * @param Request $request Objeto Request de la petición
     * @param $route Arreglo de valores asociados a la ruta accedida
     * @return array Arreglo de valores en el formato array(mensaje de respuesta, codigo http de la respuesta)
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function updateAction(Request $request, $route)
    {
        $parameters = $request->request->all();

        $entidades = $route['options'];

        $entidad = array_pop($entidades);
        $cmEntidad = $this->em->getClassMetadata($entidad);
        $columNameIdentifier = $cmEntidad->getSingleIdentifierFieldName();
        if (array_key_exists($columNameIdentifier, $parameters)) {
            $object = $this->em->getRepository($entidad)->find($parameters[$columNameIdentifier]);
        } else {
            $messageResponse = $this->trans->trans('custom_messages.idParamNotFound', array(), 'http_codes');
            return array(sprintf($messageResponse, $columNameIdentifier), 400);
        }
        if (!isset($object)) {
            /* No se encuentra el recurso se retorna un 404(Not found) */
            $messageResponse = $this->trans->trans('custom_messages.404PUT', array(), 'http_codes');
            return array(sprintf($messageResponse, $parameters[$columNameIdentifier]), 404);
        }
        $fielFields = $cmEntidad->reflFields;
        $fieldRelations = $cmEntidad->associationMappings;
        $fieldMappings = $cmEntidad->fieldMappings;
        foreach ($parameters as $key => $field) {
            if ($key == $columNameIdentifier) {
                continue;
            }
            /* pregunto si esta entre los reflected fields de la clase */
            if ($val = array_key_exists($key, $fielFields) !== false) {

                /* ahora pregunto si se encuentra entre los campos convencionales de la entidad */
                if (array_key_exists($key, $fieldMappings) !== false) {
                    $method = 'set' . ucwords($key);

                    try {
                        $object->$method($field);
                    } catch (FatalErrorException $ex) {
                        /* Mensaje informando que ha ocurrido un error interno,
                           no está implementado el método set
                           para este atributo
                        */
                        return array("error interno", 500);
                    }
                } /* si no pregunto si esta entre los campos de relaciones */
                else if (array_key_exists($key, $fieldRelations) !== false &&
                    $fieldRelations[$key]['mappedBy'] === null
                ) {
                    $method = 'set' . ucwords($key);
                    $fieldMetadata = $fieldRelations[$key];
                    if (intval($field) == 0) {
                        $messageResponse = $this->trans->trans('custom_messages.422InvIntPUT', array($key), 'http_codes');
                        return array(sprintf($messageResponse, $key, strval($field)), 422);
                    }
                    $parent = $this->em->getRepository($fieldMetadata['targetEntity'])->find($field);
                    if ($parent != null) {
                        $object->$method($parent);
                    } else {
                        /* si no se encuentra el campo de relacion entre tablas
                        especificado se devuelve un error */
                        $messageResponse = $this->trans->trans('custom_messages.422PUT', array(), 'http_codes');
                        return array(sprintf($messageResponse, $key, strval($field)), 422);
                    }
                }
            }
        }
        try {

            $this->em->persist($object);
            $this->em->flush();
            $messageResponse = $this->trans->trans('custom_messages.200PUT', array(), 'http_codes');
            return array($messageResponse, 200);
        } catch (ORMException $ex) {
            return array($ex->getMessage(), 500);
        }
    }

    /**
     * Funcionalidad que permite atender la petición de eliminacion de un recurso (DELETE)
     * @param Request $request Objeto Request de la petición
     * @param $route Arreglo de valores asociados a la ruta accedida
     * @return array Arreglo de valores en el formato array(mensaje de respuesta, codigo http de la respuesta)
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public
    function deleteAction(Request $request, $route)
    {
        $parameters = $request->request->all();
        $entidades = $route['options'];
        $entidad = array_pop($entidades);
        $cmEntidad = $this->em->getClassMetadata($entidad);
        $columNameIdentifier = $cmEntidad->getSingleIdentifierFieldName();
        if (array_key_exists($columNameIdentifier, $parameters)) {
            $object = $this->em->getRepository($entidad)->find($parameters[$columNameIdentifier]);
        } else {
            $messageResponse = $this->trans->trans('custom_messages.idParamNotFound', array(), 'http_codes');
            return array(sprintf($messageResponse, $columNameIdentifier), 400);
        }
        if (!isset($object)) {
            $messageResponse = $this->trans->trans('custom_messages.404PUT', array(), 'http_codes');
            return array(sprintf($messageResponse, $parameters[$columNameIdentifier]), 404);
        }
        try {
            $this->em->remove($object);
            $this->em->flush();
            return array($this->trans->trans('custom_messages.200Delete', array(), 'http_codes'), 200);
        } catch (\Exception $exp) {
            return array($exp->getMessage(), 500);
        }
    }

    /**
     * @return Response
     */
    public
    function partialUpdateAction()
    {
        return new Response('El verbo no está implementado aun', 501);
    }


    /**
     * Funcionalidad que brinda la url de una entidad expuesta como servicio
     *
     * @param RestService $anot Anotación para los servicios REST
     * @param ClassMetadata $entity Clases de metadatos de la entidad
     * @return string Retorna en un cadena la ruta hacia el recurso
     */
    public function obtenerRuta(RestService $anot, ClassMetadata $entity)
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
        return $ruta;
    }
} 
