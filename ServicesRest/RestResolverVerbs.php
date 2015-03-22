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
use Symfony\Component\HttpFoundation\Request;
use UCI\Boson\IntegratorBundle\Annotation\RestService;
use UCI\Boson\IntegratorBundle\Manager\IntegratorKernel;
use UCI\Boson\IntegratorBundle\Metadata\ClassMetadata;
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

    public function readAction(Request $request, array $route)
    {

        $entidades = $route['options'];
        $entidad = $entidades['id'];
        $classMetadata = $this->getRestMetadataFor($entidad);
        $classMetadata = current($classMetadata);
        $reader = new AnnotationReader();
        $dataResult = array();
        /**
         * @var ClassMetadata $classMetadata
        */
        $anotations = $reader->getClassAnnotation($classMetadata->getReflectionClass(),'UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface');
        if ($anotations instanceof RestService) {

            $servicio = new Service($this->integratorKernel);

            $name = $request->query->get('name');
            $domain = $request->query->get('domain');
            $uri = '';

            if (!is_null($name)&&!is_null($domain)) {
                $key = array(
                    'name'=>$name,
                    'domain'=>$domain,
                );

                try {
                    $uri = $servicio->get($key);
                }
                catch(IntegratorException $e)
                {
                    $dataResult['code']=HttpCode::HTTP_SERVER_ERROR;
                    return $dataResult;
                }

            }
            $dataResult['code']=HttpCode::HTTP_OK;

            if ($uri=='') {
                $dataResult['code']=HttpCode::HTTP_RESOURCE_NOTFOUND;
            }

            $dataResult['uri']=$uri;

        }
        return $dataResult;

    }

} 