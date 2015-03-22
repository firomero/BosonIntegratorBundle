<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 31/01/15
 * Time: 14:24
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class RestServicesVerbs
{
    private $em;
    private $jms;
    private $trans;
    function __construct(EntityManager $em, Serializer $jms, Translator $trans)
    {
        $this->em = $em;
        $this->jms = $jms;
        $this->trans = $trans;
    }

    public function readAction(Request $request, $route)
    {
        $entidades = $route['options'];


        $entidad = $entidades['id'];
        $cm = $this->em->getClassMetadata($entidad);
        $qb = $this->em->createQueryBuilder();
        $qb->select('a')->from($entidad, 'a');
        $start = $request->get('start');
        if ($start != '' && is_numeric($start)) {
            $qb->setFirstResult($start);
        }
        $limit = $request->get('limit');
        if ($limit != '' && is_numeric($limit)) {
            $qb->setMaxResults($limit);
        }
        if ($request->get('id') != 'all') {
            $qb->where('a.' . $cm->getSingleIdentifierFieldName() . ' = :identificator')
                ->setParameter('identificator', $request->get('id'));

            $aliasCurrent = 'a';
            $resto = array_splice($entidades, 0, 1);
            $esUnAll = false;
            foreach ($entidades as $key => $entidad) {
                $qb->innerJoin($aliasCurrent . '.' . substr($key, 2), $key);
                if ($request->get($key) != 'all') {
                    $cmMuchos = $this->em->getClassMetadata($entidad);
                    $qb->andWhere($key . '.' . $cmMuchos->getSingleIdentifierFieldName() . ' = :identificatorMuchos' . $key)
                        ->setParameter('identificatorMuchos' . $key, $request->get($key));
                    $aliasCurrent = $key;
                    $qb->addSelect(array($aliasCurrent));
                } else {
                    $aliasCurrent = $key;
                    $qb->addSelect(array($aliasCurrent));
                    $esUnAll = true;
                    break;
                }
            }
            $this->addSearchParameters($request, $qb, $aliasCurrent);
            $arrayObjects = $qb->getQuery()->getArrayResult();
            if ($esUnAll) {
                $respuesta = array();
                foreach ($arrayObjects as $value) {
                    array_push($respuesta, $value[substr($aliasCurrent, 2)]);
                }
                return $this->jms->serialize($respuesta, 'json');
            } else {
                if ($aliasCurrent == 'a') {
                    return $this->jms->serialize($arrayObjects[0], 'json');
                } else  return $this->jms->serialize($arrayObjects[0][substr($aliasCurrent, 2)], 'json');

            }
        } else {
            $this->addSearchParameters($request, $qb, 'a', true);
            $arrayObjects = $qb->getQuery()->getArrayResult();
            return $this->jms->serialize($arrayObjects, 'json');
        }

    }

    private function addSearchParameters(Request $request, QueryBuilder $qb, $alias = 'a', $esUnAll = false)
    {
         $parameters = $request->query->all();
        if ($alias == 'a' && $esUnAll) {
            foreach ($parameters as $key => $param) {
                if ($key != 'start' && $key != 'limit' && $key != 'XDEBUG_SESSION_START') {
                    $qb->where($alias . '.' . $key . ' LIKE :' . $key . 'searchparameter')
                        ->setParameter($key . 'searchparameter','%'.$param.'%');
                    array_splice($parameters, 0, 1);
                    break;
                }
                array_splice($parameters, 0, 1);
            }
        }
        foreach ($parameters as $key => $param) {
            if ($key != 'start' && $key != 'limit' && $key != 'XDEBUG_SESSION_START') {
                $qb->andWhere($alias . '.' . $key . ' LIKE :' . $key . 'searchparameter')
                    ->setParameter($key . 'searchparameter', '%'.$param.'%');
            }
        }

    }

//    private function recursiveQueryBuild(Request $request,QueryBuilder $qb,$entidades,$alias)
//    {
//        //condicion de parada
//        if($entidades == null)
//            return $qb;
//        //bloque recursivo
//        $keys = array_keys($entidades);
//        $key = $keys[0];
//        $entidad = $entidades[$key];
//        $qb->innerJoin($alias . '.' . substr($entidad, 2), $key);
//            if ($request->get($key) != 'all') {
//                $cmMuchos = $this->em->getClassMetadata($entidad);
//                $qb->andWhere($key . '.' . $cmMuchos->getSingleIdentifierFieldName() . ' = :identificatorMuchos')
//                    ->setParameter('identificatorMuchos', $request->get($key));
//            }
//
//            return $this->recursiveQueryBuild($request,$qb,array_shift($entidades),$key);
//        //fin del bloque recursivo
//    }

    public function createAction(Request $request, $route)
    {
        $codigo = 200;
        $message = '';
        $parameters = $request->request->all();
        //$parametersRuta = $request->attributes->get('_route_params');

        $entidades = $route['options'];

        $entidad = array_pop($entidades);
        $cmMuchos = $this->em->getClassMetadata($entidad);
        $object = new $entidad;
        $fielFields = $cmMuchos->reflFields;
        $fieldRelations = $cmMuchos->associationMappings;
        $fieldMappings = $cmMuchos->fieldMappings;
        foreach ($parameters as $key =>$field) {
            /*pregunto si esta entre los reflected fields de la clase*/
            if($val = array_key_exists($key,$fielFields) !== false){

                /*ahora pregunto si se encuentra entre los campos convencionales de la entidad*/
                if(array_key_exists($key,$fieldMappings)!== false)
                {
                    $method = 'set'.ucwords($key);
                    $object->$method($field);
                }
                /*si no pregunto si esta entre los campos de relaciones */
                else if(array_key_exists($key,$fieldRelations)!== false && $fieldRelations[$key]['mappedBy'] === null){
                    $method = 'set'.ucwords($key);
                    $fieldMetadata = $fieldRelations[$key];
                    $parent = $this->em->getRepository($fieldMetadata['targetEntity'])->find($field);
                    if($parent != null)
                    {
                        $object->$method($parent);
                    }
                }
            }
        }
        try{

            $this->em->persist($object);
            $this->em->flush();

        }
        catch(ORMException $ex){
            return $ex->getMessage();
        }        /*foreach ($cmMuchos->associationMappings as $asociacion) {
            if($asociacion['mappedBy'] === null){
                if(array_key_exists('id'.$asociacion['fieldName'],$parametersRuta)){
                    $method = 'set'.ucwords($asociacion['fieldName']);
                    $object->$method($parametersRuta['id'.$asociacion['fieldName']]);
                }
            }
        }*/
    }

    public function updateAction(Request $request, $route)
    {
        $codigo = 200;
        $message = '';
        $parameters = $request->request->all();
        //$parametersRuta = $request->attributes->get('_route_params');

        $entidades = $route['options'];

        $entidad = array_pop($entidades);
        $cmMuchos = $this->em->getClassMetadata($entidad);
        $columNameIdentifier = $cmMuchos->getSingleIdentifierFieldName();

        $object = $this->em->getRepository($entidad)->find($parameters[$columNameIdentifier]);
        if(!isset($object)){
            /* No se encuentra el recurso se retorna un 404(Not found) */
        }
        $fielFields = $cmMuchos->reflFields;
        $fieldRelations = $cmMuchos->associationMappings;
        $fieldMappings = $cmMuchos->fieldMappings;
        foreach ($parameters as $key =>$field) {
            if($key == $columNameIdentifier){
                continue;
            }
            /* pregunto si esta entre los reflected fields de la clase */
            if($val = array_key_exists($key,$fielFields) !== false){

                /* ahora pregunto si se encuentra entre los campos convencionales de la entidad */
                if(array_key_exists($key,$fieldMappings)!== false)
                {
                    $method = 'set'.ucwords($key);
                    $object->$method($field);
                }
                /* si no pregunto si esta entre los campos de relaciones */
                else if(array_key_exists($key,$fieldRelations)!== false && $fieldRelations[$key]['mappedBy'] === null){
                    $method = 'set'.ucwords($key);
                    $fieldMetadata = $fieldRelations[$key];
                    $parent = $this->em->getRepository($fieldMetadata['targetEntity'])->find($field);
                    if($parent != null)
                    {
                        $object->$method($parent);
                    }
                    else
                    {
                        /* si no se encuentra el campo de relacion entre tablas
                        especificado se devuelve un error */
                        $messageResponse = $this->trans->trans('custom_messages.422PUT',array(),'http_codes');
                        return new Response(sprintf($messageResponse,$key,strval($field)), 422);
                    }
                }
            }
        }
        try{

            $this->em->persist($object);
            $this->em->flush();

        }
        catch(ORMException $ex){
            return $ex->getMessage();
        }
    }

    public function partialUpdateAction()
    {

    }

    public function deleteAction()
    {

    }
} 