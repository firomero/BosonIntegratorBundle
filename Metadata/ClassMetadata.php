<?php

namespace UCI\Boson\IntegratorBundle\Metadata;

//use Metadata\ClassMetadata as BaseClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadata as BaseClassMetadata;
/**
 * ClassMetadata.
 *
 * Exposes a simple interface to read objects metadata.
 *
 * @author Felix Romero <firomero@uci.cu>
 */
class ClassMetadata extends BaseClassMetadata
{
    public $fields = array();
    public $reflection;

    public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->fields
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->fields
            ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }

    public function getReflectionClass()
    {
        if (is_null($this->reflection)) {
            $this->reflection = new \ReflectionClass($this->name);
        }

        return $this->reflection;

    }
}
