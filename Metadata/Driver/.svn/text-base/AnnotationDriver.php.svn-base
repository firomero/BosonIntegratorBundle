<?php

namespace UCI\Boson\IntegratorBundle\Metadata\Driver;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use UCI\Boson\IntegratorBundle\Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;


/**
 * Annotation driver
 *
 * @author Felix Ivan Rommero Rodriguez <firomero@uci.cu>
 */
class AnnotationDriver implements DriverInterface
{
    const REST_ANNOTATION = 'UCI\Boson\IntegratorBundle\Annotation\ServiceAnnotationInterface';

    protected $reader;

    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        

        $metadata = new ClassMetadata($class->name);

        return $metadata;
    }


}
