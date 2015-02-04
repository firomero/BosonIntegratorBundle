<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 28/12/2014
 * Time: 16:42
 */

namespace IntegratorBundle\Integrator\Configurator;
use Doctrine\Common\Collections\ArrayCollection;
use IntegratorBundle\Integrator\Configurator\ConfigurationReaderInterface;
use Symfony\Component\DependencyInjection\SimpleXMLElement;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class XmlConfigurationReader implements  ConfigurationReaderInterface{

    protected $depends;
    protected $resolved;


    public function __construct($depends, $resolved)
    {
        $this->depends = $depends;
        $this->resolved = $resolved;

    }

    /**
     * @param null $path
     * @param $type
     * @return null
     */
    public function loadConfiguration($type,$path=null)
    {
       $return = $this->depends;
        if (isset($path)) {
            $return = $path;
        }
        switch ($type) {
            case ConfigurationReaderInterface::DEPENDENCY:
                break;
            case ConfigurationReaderInterface::RESOLVED:
                $return = $this->resolved;
                break;

        }
        return $return;
    }

    /**
     * @return array
     */
    public function getDependencyList()
    {
        /** @var TYPE_NAME $xml */
        $xml = $this->loadConfiguration(ConfigurationReaderInterface::DEPENDENCY);
        $iterator = new \SimpleXMLIterator($xml);
        $dependencies = array();
        $normalizer = new GetSetMethodNormalizer();
        $encoder = new XmlEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            if ($iterator->hasChildren()) {
                $dependencies[]= $serializer->deserialize($iterator->current()->asXML(), 'IntegratorBundle\\Model\\Dependency', 'xml');
            }
        }


        return $dependencies;
    }

    /**
     * @return ArrayCollection
     */
    public function getResolvedList()
    {
        /** @var TYPE_NAME $xml */
        $xml = $this->loadConfiguration(ConfigurationReaderInterface::RESOLVED);
        $iterator = new \SimpleXMLIterator($xml);
        $resolved = array();
        $normalizer = new GetSetMethodNormalizer();
        $encoder = new XmlEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            if ($iterator->hasChildren()) {
                $resolved[]= $serializer->deserialize($iterator->current()->asXML(), 'IntegratorBundle\\Model\\Dependency', 'xml');
            }
        }


        return $resolved;
    }

    public function getXmlRepresentation($type)
    {
        $file = $this->loadConfiguration($type);
        $xml = new SimpleXMLElement($file);
        return $xml->asXML();
    }
}