<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 12/03/15
 * Time: 16:49
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;


use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\Container;
use UCI\Boson\IntegratorBundle\Metadata\Driver\AnnotationDriver;
use UCI\Boson\IntegratorBundle\Metadata\Driver\FileLocator;

/**
 * Utilidad para el manejor de metadatos y modelos
 * Class UtilRestDiscover
 * @package UCI\Boson\IntegratorBundle\ServicesRest
 */
trait UtilRestDiscover {


    protected $noModels;


    /**
     * Genera los metadatos de clases que no son entidades
     * @return array
     */
    public function getRestMetadata($model=array())
    {
        if (sizeof($model)<1) {
            $model = array(__DIR__.'/../Model');
        }




        $fileLocator = new FileLocator($model);
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
    public function getRestMetadataFor($entity,$model=array(),$options)
    {
        if (sizeof($model)<1) {
            $model = array(__DIR__.'/../Model');
        }

        $modelModified = array();
        foreach ($model as $m) {

            $dir = $options.'/../src/'.$m;
            $dir = str_replace('\\','/',$dir);
            array_push($modelModified,$dir);
        }


        $fileLocator = new FileLocator($modelModified);
        $class = $fileLocator->findFileClass(new \ReflectionClass($entity),'php');



        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $metadatos = array();

//    ladybug_dump($class->getFilename());exit;

        array_push($metadatos,$driver->loadMetadataForClass($this->getClassFromFile($class)));


        return $metadatos;
    }

    /**
     * Devuelve una colección de directorios rest
     * @param Container $container
     * @return array
     */
    public  function getModels(Container $container)
    {
        $globalName = $container->getParameter('global_name');
        $globalDomain = $container->getParameter('default_domain');

        $kernel = $container->get('kernel');

        $bundles = $kernel->getBundles();

        $namespace = array();
        $models = array();
        $nombres = array();


        foreach ($bundles as $b) {
            $nombres[]=$b->getNamespace();

            if ($this->NameComparer($b->getNamespace(),$globalName)) {
                if (file_exists($b->getPath().'/'.$globalDomain)&&is_dir($b->getPath().'/'.$globalDomain)) {

                    array_push($namespace,$b->getPath().'/'.$globalDomain);
                    array_push($models,$b->getNamespace().'\\'.$globalDomain);


                }
            }
        }

        $this->setNoModels($models);



        return $namespace;
    }

    /**
     * Compara los nombres para determinar las rutas de los modelos personalizados.
     * @param $bundlename
     * @param $globalname
     * @return bool
     */
    protected function NameComparer($bundlename,$globalname)
    {
        $result = strpos($bundlename,$globalname);
        if ($result!==false) {
             return true;
        }

        return true;

    }

    /**
     * Obtiene el nombre de la Clase
     * @param \SplFileInfo $file
     * @return \ReflectionClass
     */
    protected  function getClassFromFile(\SplFileInfo $file)
    {
        $classpath = $file->getBasename('.php');
        $models = $this->getNoModels();
        $path = $file->getPath();

        $className = 'UCI\\Boson\\IntegratorBundle\\Model\\'.$classpath;
        $path = str_replace('/','\\',$path);

        if (is_array($models)) {
            foreach ($models as $model) {
                if (strpos($path,$model)) {
                    $className = $model.'\\'.$classpath;
                }
            }
        }
        $className = str_replace('/','\\',$className);



        return new \ReflectionClass($className);
    }

    protected  function setNoModels(array $models)
    {
        $this->noModels = $models;
    }

    protected  function getNoModels()
    {
        return $this->noModels;
    }




} 