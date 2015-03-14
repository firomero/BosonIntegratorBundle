<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/02/15
 * Time: 17:07
 */

namespace UCI\Boson\IntegratorBundle\Annotation;


use Doctrine\Common\Annotations\Annotation\Enum;
use UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface;


/**
 * @Annotation()
 * @Target("PROPERTY")
 *
 * Class Dependency
 * @package UCI\Boson\IntegratorBundle\Annotation
 */
class Dependency implements ServiceAnnotationInterface {

    /**
     * @var string
     */
    public $targetEntity;

    /**
     * @Enum({"array","object"})
     */
    public $type;

    /**
     * @var boolean
     */
    public $handled;
}
