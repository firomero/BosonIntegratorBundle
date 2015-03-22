<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 12/03/15
 * Time: 16:49
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;


use Doctrine\Common\Annotations\AnnotationReader;
use UCI\Boson\IntegratorBundle\Metadata\Driver\AnnotationDriver;
use UCI\Boson\IntegratorBundle\Metadata\Driver\FileLocator;

trait UtilRestDiscover {



    /**
     * Genera los metadatos de clases que no son entidades
     * @return array
     */
    public function getRestMetadata()
{
    $model = __DIR__.'/../Model';

   $fileLocator = new FileLocator(array($model));
   $classes = $fileLocator->findAllClasses('php');

   $reader = new AnnotationReader();
   $driver = new AnnotationDriver($reader);
    $metadatos = array();

    foreach ($classes as $class) {

        array_push($metadatos,$driver->loadMetadataForClass($this->getClassFromFile($class)));
    }

    return $metadatos;
}

    /**
     * Devuelve los metadatos para una clase en espec'ifico
     * @param $entity
     * @return array
     */
    public function getRestMetadataFor($entity)
{
    $model = __DIR__.'/../Model';

    $fileLocator = new FileLocator(array($model));
    $class = $fileLocator->findFileClass(new \ReflectionClass($entity),'php');


    $reader = new AnnotationReader();
    $driver = new AnnotationDriver($reader);
    $metadatos = array();

        array_push($metadatos,$driver->loadMetadataForClass($this->getClassFromFile($class)));


    return $metadatos;
}

    /**
     * Obtiene el nombre de la Clase
     * @param \SplFileInfo $file
     * @return \ReflectionClass
     */
    protected  function getClassFromFile(\SplFileInfo $file)
    {
       $classpath = $file->getBasename('.php');
       $className = 'UCI\\Boson\\IntegratorBundle\\Model\\'.$classpath;
        return new \ReflectionClass($className);
    }

} 