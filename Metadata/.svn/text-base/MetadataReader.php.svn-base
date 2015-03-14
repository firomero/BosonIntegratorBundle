<?php

namespace UCI\Boson\IntegratorBundle\Metadata;

use Metadata\AdvancedMetadataFactoryInterface;

/**
 * MetadataReader.
 *
 * Exposes a simple interface to read objects metadata.
 *
 * @author Felix Romero <firomero@uci.cu>
 */
class MetadataReader
{
    /**
     * @var AdvancedMetadataFactoryInterface $reader
     */
    protected $reader;

    /**
     * Constructs a new instance of the MetadataReader.
     *
     * @param AdvancedMetadataFactoryInterface $reader The "low-level" metadata reader.
     */
    public function __construct(AdvancedMetadataFactoryInterface $reader)
    {
        $this->reader = $reader;
    }



    /**
     * Search for all rest classes.
     *
     * @return array A list of rest class names.
     */
    public function getRestClasses()
    {
        return $this->reader->getAllClassNames();
    }

    /**
     * Attempts to read the rest fields.
     *
     * @param string $class The class name to test (FQCN).
     *
     * @return array A list of rest fields.
     */
    public function getRestFields($class)
    {
        $metadata = $this->reader->getMetadataForClass($class);
        $restFields = array();

        foreach ($metadata->classMetadata as $classMetadata) {
            $restFields = array_merge($restFields, $classMetadata->fields);
        }

        return $restFields;
    }




}
