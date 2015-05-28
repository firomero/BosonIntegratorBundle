<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 18/12/2014
 * Time: 17:58
 */

namespace IntegratorBundle\Tests\Configurator;

use IntegratorBundle\Model\Dependency;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
require __DIR__.'./../../Model/Dependency.php';


class XmlConfigurationReaderTest extends \PHPUnit_Framework_TestCase
{
    public function getDependencyListTest()
    {
    }
    private function loadDependencyDataList()
    {
        $xml = '<list>
    <depedency>
        <name>d1</name>
        <version>1.2</version>
        <uri>main</uri>
        <depends></depends>
    </depedency>
    <depedency>
        <name>d2</name>
        <version>1.2</version>
        <uri>main</uri>
        <depends>
            <name>d3</name>
            <version>dev-master</version>
            <uri>local/main</uri>
        </depends>
    </depedency>
    <depedency>
        <name>d1</name>
        <version>1.2</version>
        <uri>main</uri>
        <depends></depends>
    </depedency>
 </list>
';


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

    public function testisEmpty()
    {
        $deps = $this->loadDependencyDataList();
        $nmber = count($deps);

        $this->assertEquals($nmber, 3);
    }

    public function testClass()
    {
        $deps = new Dependency();
        $class = get_class($deps);
        $this->assertEquals($class, 'IntegratorBundle\\Model\\Dependency');
    }

    public function testObject()
    {
        $deps = $this->loadDependencyDataList();
        $class =get_class($deps[0]) ;
        $this->assertEquals($class, 'IntegratorBundle\\Model\\Dependency');
    }
}
