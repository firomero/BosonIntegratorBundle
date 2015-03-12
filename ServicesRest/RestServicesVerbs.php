<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 31/01/15
 * Time: 14:24
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class RestServicesVerbs
{
    private $em;
    private $jms;
    function __construct(EntityManager $em,Serializer $jms)
    {
        $this->em = $em;
        $this->jms = $jms;
    }

    public function readAction(Request $request, $route){
        $entidades = $route['options'];


            $entidad = $entidades['id'];
            $cm = $this->em->getClassMetadata($entidad);
            $qb = $this->em->createQueryBuilder();
            $qb->select('a')->from($entidad, 'a');

        if($request->get('id') != 'all')
            {
                $qb->where('a.'.$cm->getSingleIdentifierFieldName().' = :identificator' )
                    ->setParameter('identificator',$request->get('id'));

            $aliasCurrent = 'a';
            $Resto =array_splice($entidades,0,1);
            $esUnAll = false;
            foreach($entidades as $key => $entidad){
                $qb->innerJoin($aliasCurrent . '.' . substr($key, 2), $key);
                if ($request->get($key) != 'all')
                {
                    $cmMuchos = $this->em->getClassMetadata($entidad);
                    $qb->andWhere($key . '.' . $cmMuchos->getSingleIdentifierFieldName() . ' = :identificatorMuchos'.$key)
                        ->setParameter('identificatorMuchos'.$key, $request->get($key));
                    $aliasCurrent = $key;
                    $qb->addSelect(array($aliasCurrent));
                }
                else
                {   $aliasCurrent = $key;
                    $qb->addSelect(array($aliasCurrent));
                    $esUnAll = true;
                    break;
                }
            }
                $arrayObjects = $qb->getQuery()->getArrayResult();
                if($esUnAll){
                    $respuesta = array();
                    foreach($arrayObjects as $value){
                        array_push($respuesta,$value[substr($aliasCurrent, 2)]);
                    }
                    return $this->jms->serialize($respuesta,'json');
                }
                else{
                    ladybug_dump($qb->getQuery());
                    ladybug_dump($arrayObjects);
                    if($aliasCurrent == 'a'){
                        return $this->jms->serialize($arrayObjects[0],'json');
                    }
                    else  return $this->jms->serialize($arrayObjects[0][substr($aliasCurrent, 2)],'json');

                }
            }
        else {
            $arrayObjects =  $qb->getQuery()->getArrayResult();
            return $this->jms->serialize($arrayObjects,'json');
        }

    }

    private function recursiveQueryBuild(Request $request,QueryBuilder $qb,$entidades,$alias)
    {
        //condicion de parada
        if($entidades == null)
            return $qb;
        //bloque recursivo
        $keys = array_keys($entidades);
        $key = $keys[0];
        $entidad = $entidades[$key];
        $qb->innerJoin($alias . '.' . substr($entidad, 2), $key);
            if ($request->get($key) != 'all') {
                $cmMuchos = $this->em->getClassMetadata($entidad);
                $qb->andWhere($key . '.' . $cmMuchos->getSingleIdentifierFieldName() . ' = :identificatorMuchos')
                    ->setParameter('identificatorMuchos', $request->get($key));
            }

            return $this->recursiveQueryBuild($request,$qb,array_shift($entidades),$key);
        //fin del bloque recursivo
    }

    public function createAction(){
        ladybug_dump("hello post");
    }

    public function updateAction(){

    }

    public function partialUpdateAction(){

    }

    public function deleteAction(){

    }
} 