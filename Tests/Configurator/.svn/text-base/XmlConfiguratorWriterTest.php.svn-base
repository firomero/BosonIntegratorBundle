<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 22/12/2014
 * Time: 19:27
 */

namespace IntegratorBundle\Tests\Configurator;
use IntegratorBundle\Model\Dependency;
use Symfony\Component\DependencyInjection\SimpleXMLElement;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class XmlConfiguratorWriterTest extends  \PHPUnit_Framework_TestCase
{
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

    public function testInsertDependency()
    {
        $dep = new Dependency();

        $dep->setDepends(array());

        $dep->setName('custom/package');

        $dep->setVersion('1.2');

        $list = $this->loadDependencyDataList();

        $nmb = count($list);

        array_push($list, $dep);

        $this->assertEquals($nmb+1,count($list));

    }

    public function testOutPut()
    {
        $dep = new Dependency();

        $dep->setDepends(array());

        $dep->setName('custom/package');

        $dep->setVersion('1.2');

        $list = $this->loadDependencyDataList();

        $listString = '';

        array_push($list, $dep);

        $normalizer = new GetSetMethodNormalizer();

        $encoder = new XmlEncoder('dependency');

        $serializer = new Serializer(array($normalizer), array($encoder));

        foreach ($list as $item) {

         $listString.= $serializer->serialize($item,'xml');

        }
        $listString = sprintf('<list>%s</list>',$listString);
        $listString = str_replace('<?xml version="1.0"?>',"",$listString);

        $this->assertContains('custom',$listString,$listString);

    }
} 