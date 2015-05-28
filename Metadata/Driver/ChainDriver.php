<?php

namespace UCI\Boson\IntegratorBundle\Metadata\Driver;

use Metadata\Driver\AdvancedDriverInterface;

/**
 * Class ChainDriver
 * @package UCI\Boson\IntegratorBundle\Metadata\Driver
 */
class ChainDriver implements AdvancedDriverInterface
{
    /**
     * @var array
     */
    protected $drivers;

    /**
     * @param array $drivers
     */
    public function __construct(array $drivers = array())
    {
        $this->drivers = $drivers;
    }

    /**
     * @param DriverInterface $driver
     */
    public function addDriver(DriverInterface $driver)
    {
        $this->drivers[] = $driver;
    }

    /**
     * @param \ReflectionClass $class
     * @return null
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        foreach ($this->drivers as $driver) {
            if (null !== ($metadata = $driver->loadMetadataForClass($class))) {
                return $metadata;
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames()
    {
        $classes = array();
        foreach ($this->drivers as $driver) {
            if (!$driver instanceof AdvancedDriverInterface) {
                continue;
            }

            $driverClasses = $driver->getAllClassNames();
            if (!empty($driverClasses)) {
                $classes = array_merge($classes, $driverClasses);
            }
        }

        return $classes;
    }
}
