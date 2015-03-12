<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/01/15
 * Time: 15:59
 */

namespace UCI\Boson\IntegratorBundle\Annotation;



use Doctrine\Common\Annotations\Annotation\Attribute;
use UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface;
/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *   @Attribute("name", type = "string", required=true),
 *   @Attribute("domain", type="string"),
 *   @Attribute("version", type="string"),
 *   @Attribute("optional", type="boolean"),
 * })
 */
class RestServiceConsume  implements ServiceAnnotationInterface {



    public $name;

    public $domain;

    public $version;

    private  $optional;




    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;

    }

    /**
     * @return mixed
     */
    public function getOptional()
    {
        if($this->optional == null)
            return true;
        $this->optional;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }


}