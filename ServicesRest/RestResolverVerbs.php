<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 19/03/15
 * Time: 16:43
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;


use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use UCI\Boson\IntegratorBundle\Annotation\RestService;
use UCI\Boson\IntegratorBundle\Manager\IntegratorKernel;
use UCI\Boson\IntegratorBundle\Metadata\ClassMetadata;
use UCI\Boson\IntegratorBundle\Model\AbstractResource;
use UCI\Boson\IntegratorBundle\Model\IntegratorException;
use UCI\Boson\IntegratorBundle\Model\Recurso;
use UCI\Boson\IntegratorBundle\Model\Service;

/**
 * Esta clase tiene la responsabilidad de resolver las entidades que no perteneces a doctrine. Generalmente modelos del negocio que devuelven una respuesta
 * Class RestResolverVerbs
 * @package UCI\Boson\IntegratorBundle\ServicesRest
 */
class RestResolverVerbs {

    use UtilRestDiscover;
    use ContentTypeMatcher;
    protected $integratorKernel;
    protected $jms;

    public function __construct(IntegratorKernel $kernel, Serializer $jms)
    {
        $this->integratorKernel = $kernel;
        $this->jms = $jms;
    }

    public function readAction(Request $request, array $route, $container)
    {

        $entidades = $route['options'];
        $entidad = $entidades['id'];
        $parts = explode('Model',$entidad);
        /**
        * @var Container $container
         */
        $appDir = $container->getParameter('kernel.root_dir');
        $this->getModels($container);
        $classMetadata = $this->getRestMetadataFor($entidad, array(current($parts).'Model'),$appDir);
        $classMetadata = current($classMetadata);
        $reader = new AnnotationReader();
        $dataResult = array();
        /**
         * @var ClassMetadata $classMetadata
        */
        $anotations = $reader->getClassAnnotation($classMetadata->getReflectionClass(),'UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface');
        if ($anotations instanceof RestService) {

           $class = $classMetadata->getReflectionClass()->getName();



            $name = $request->query->get('name');
            $domain = $request->query->get('domain');
            $result = '';



            if (!is_null($name)&&!is_null($domain)) {
                $key = array(
                    'name'=>$name,
                    'domain'=>$domain,
                );

                $servicio = new $class($container);

                try {
                    /**
                     * @var Recurso $servicio
                     */
                    $result = $servicio->get($key);
                }
                catch(\Exception $e)
                {
                    $dataResult[0]=array();
                    $dataResult[1]=HttpCode::HTTP_SERVER_ERROR;

                    return $dataResult;
                }

                $dataResult[0]=$result;
                $dataResult[1]=HttpCode::HTTP_OK;
                if ($result=='') {
                    $dataResult[1]=HttpCode::HTTP_RESOURCE_NOTFOUND;
                }

            }
            else
            {

                /**
                 * @var AbstractResource $servicio
                 */
                $servicio = new $class($container);
                $dataResult[0] = $servicio->get($request);
                $dataResult[1]=HttpCode::HTTP_OK;


                if (is_null($dataResult[1])) {
                    $dataResult[1]=array();
                    $dataResult[1]=HttpCode::HTTP_RESOURCE_NOTFOUND;
                }


            }
        }
        return $dataResult;

    }

} 