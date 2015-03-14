<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 20/02/15
 * Time: 11:43
 */

namespace IntegratorBundle\Tests\Guzzle;





use GuzzleHttp\Client;

class GuzzTest extends \PHPUnit_Framework_TestCase{

    /**
     *Prueba la instancia de la clase guzz
     */
    public function testGuzz()
    {
        $client = new Client();
        $class = get_class($client);

        $this->assertNotEmpty($class);
    }


    /**
     *Ejemplo de como obtener el json decodificado
     */
    public function testConsume()
    {
        $client = new Client();
        $response = $client->get('http://localhost/rest/');

        $json = $response->json();

        $this->assertNotEmpty($json);


    }

    public function testTextGuzzle()
    {
        $client = new Client();
        $response = $client->get('http://localhost/rest/');

        $status = $response->getStatusCode();

        $this->assertEquals(200,$status);
    }

    public function testMultipleConsumo()
    {
        $recursos = array();
        $iterator = rand(2,5);
        $client = new Client();

        for ($i = 0; $i<$iterator;$i++) {

            $response = $client->get('http://localhost/rest/');
            array_push($recursos,$response->json());
        }

        $this->assertGreaterThan(0,sizeof($recursos));
    }

    public function testFlatten()
    {
        $recursos = array();
        $arrayResult = array(
            'dependencias'=>array(),
            'servicios'=>array(),
        );
        $iterator = rand(2,5);
        $client = new Client();

        for ($i = 0; $i<$iterator;$i++) {

            $response = $client->get('http://localhost/rest/');
            array_push($recursos,$response->json());
        }

        foreach ($recursos as $resource) {
            array_push($arrayResult['dependencias'],$resource['dependencias']);
            array_push($arrayResult['servicios'],$resource['servicios']);
        }
        $key = array_key_exists('dependencias',$arrayResult);

        var_export($arrayResult);

        $this->assertTrue($key);
    }

}