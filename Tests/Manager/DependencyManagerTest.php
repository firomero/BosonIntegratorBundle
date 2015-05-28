<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 22/12/2014
 * Time: 19:47
 */

namespace IntegratorBundle\Tests\Manager;
use IntegratorBundle\Model\Dependency;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
require __DIR__.'./../../Model/Dependency.php';


class DependencyManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {

        $id = 'd1';
        $list = $this->loadDependencyDataList();
        $data = array();
        foreach ($list as $item) {
            if ($item->getName()=='d1') {
                $data = $item;
            }
        }

        $this->assertEquals($id,$data->getName());
    }

    public function testgetAll()
    {
        $list = $this->loadDependencyDataList();
        $number = count($list);
        $this->assertGreaterThanOrEqual(1,$number,'oops, amount unexpected');
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

} 