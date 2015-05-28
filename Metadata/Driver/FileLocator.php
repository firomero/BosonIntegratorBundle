<?php

namespace UCI\Boson\IntegratorBundle\Metadata\Driver;

use Metadata\Driver\AdvancedFileLocatorInterface;
use Symfony\Component\Finder\Finder;

/**
 * Interfaz para el trabajo con ficheros
 * Class FileLocator
 * @package UCI\Boson\IntegratorBundle\Metadata\Driver
 */
class FileLocator implements AdvancedFileLocatorInterface
{
    /**
     * @var array
     */
    private $dirs;

    /**
     * @param array $dirs
     */
    public function __construct(array $dirs)
    {
        $this->dirs = $dirs;
    }

    /**
     * @return array
     */
    public function getDirs()
    {
        return $this->dirs;
    }

    /**
     * @param \ReflectionClass $class
     * @param string           $extension
     *
     * @return string|null
     */
    public function findFileForClass(\ReflectionClass $class, $extension)
    {
        $finder = Finder::create();

        foreach ($this->dirs as $prefix => $dir) {
            if ('' !== $prefix && 0 !== strpos($class->getNamespaceName(), $prefix)) {
                continue;
            }

            $files = $finder->files()->in($dir)->name(sprintf('%s.%s', $class->getShortName(), $extension));

            if (count($files) !== 1) {
                continue;
            }

            $file = current(iterator_to_array($files));

            return $file->getPathname();
        }

        return null;
    }

    /**
     * Finds all possible metadata files.
     *
     * @param string $extension
     *
     * @return array
     */
    public function findAllClasses($extension)
    {
        if (empty($this->dirs)) {
            return array();
        }

        $files = Finder::create()
            ->files()
            ->name('*.'.$extension)
            ->in($this->dirs);

        return iterator_to_array($files);
    }

    /**
     * @param \ReflectionClass $class
     * @param string           $extension
     *
     * @return \SplFileInfo
     */
    public function findFileClass(\ReflectionClass $class, $extension)
    {
             $dir = $this->dirs;

             $finder = Finder::create();

            $files = $finder->files()->in($dir)->name(sprintf('%s.%s', $class->getShortName(), $extension));

            if (count($files) !== 1) {
                return null;
            }

            $file = current(iterator_to_array($files));

            return $file;



    }


}
