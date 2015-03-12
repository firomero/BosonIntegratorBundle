<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 29/01/15
 * Time: 13:46
 */

namespace IntegratorBundle\Tests\Locator;


class ServiceLocatorTest extends \PHPUnit_Framework_TestCase {

    /**
     *Metodo que se encarga de normalizar el json con las representaciones
     */
    public function testNormalizejson()
    {
        $json = $this->loadDefault();

        $normalizedData = json_decode($json,true);



        $this->assertArrayHasKey('title',$normalizedData);

    }

    /**
     *Se serciora de que exista una definicio
     */
    public function testDefiniciones()
    {
        $file = file_exists('definition.json');

        $this->assertFalse($file);
    }

    /**
     *Devuelve una configuracion por defecto del componente
     */
    public function loadDefault()
    {
       $definition = '{
    "title":"Definiciones",
    "dependencias":[
        {
            "name":"Stub1",
            "domain":"StubDomain",
            "version":"1.2",
            "optional":"true",
            "editable":"false",
            "bundlename":"pepe",
            "type":"dependencia",
            "properties":[
                {
                    "stub4name":"string",
                    "stub4id":"integer",
                    "stub4ci":"string",
                    "stub4date":"datetime",
                    "stub4uri":"uri"
                }
            ]
        },
        {
            "name":"Stub2",
            "domain":"StubDomain",
            "version":"1.1",
            "optional":"true",
            "editable":"true",
            "bundlename":"juan",
            "type":"dependencia",
            "properties":[
                {
                    "stub2name":"string",
                    "stub2id":"integer",
                    "stub2ci":"string",
                    "stub2date":"datetime",
                    "stub2uri":"uri"
                }
            ]
        },
        {
            "name":"Stub3",
            "domain":"StubDomain",
            "version":"0.2",
            "optional":"true",
            "editable":"false",
            "bundlename":"lolo",
            "type":"dependencia",
            "properties":[
                {
                    "stub3name":"string",
                    "stub3id":"integer",
                    "stub3ci":"string",
                    "stub3date":"datetime"
                }
            ]
        }
    ],
    "servicios":[
        {
            "name":"Stub4",
            "domain":"StubDomain",
            "version":"1.4",
            "optional":"true",
            "editable":"false",
            "bundlename":"julio",
            "uri":"http://api/rest/stub4",
            "type":"servicio",
            "properties":[
                {
                    "stub4name":"string",
                    "stub4id":"integer",
                    "stub4ci":"string",
                    "stub4date":"datetime",
                    "stub4uri":"uri"

                }
            ]
        },
        {
            "name":"Stub5",
            "domain":"StubDomain",
            "version":"1.1",
            "optional":"true",
            "editable":"true",
            "bundlename":"pepe",
            "uri":"http://api/rest/stub5",
            "type":"servicio",
            "properties":[
                {
                    "stub5name":"string",
                    "stub5id":"integer",
                    "stub5ci":"string",
                    "stub5date":"datetime"
                }
            ]
        },
        {
            "name":"Stub6",
            "domain":"StubDomain",
            "version":"0.2",
            "bundlename":"info",
            "optional":"true",
            "editable":"false",
            "uri":"http://api/rest/stub6",
            "type":"servicio",
            "properties":[
                {
                    "stub6name":"string",
                    "stub6id":"integer",
                    "stub6ci":"string",
                    "stub6date":"datetime"
                }
            ]
        }
    ]
}';
        return $definition;
    }

    /**
     *Prueba que se ordenen los elementos teniendo en cuenta la funcion tipada
     */
    public function testOrden()
    {
       $recursos = $this->loadDataGraphArray();
       $this->sortBy('type', $recursos);

        $type = $recursos[0]['type'];


        $this->assertEquals('dependencia',$type,'Has fallado el resultado es '.$type);


    }

    /**
     *Construye el grafo inicial para recorrer[Grafo Disperso a convertir en un Dependency Graph o en un DAG]
     */
    public function testBuildFirstGraph()
    {
        $inicialGrafo = array();

        $seed = rand(7,12);

        while($seed > 0)
        {
           $inicialGrafo=array_merge($inicialGrafo,$this->loadDataGraphArray());
            $seed--;
        }


        $this->assertArrayNotHasKey('dependencias',$inicialGrafo);
        $this->assertArrayNotHasKey('servicios',$inicialGrafo);
        $this->assertFalse(count($inicialGrafo)==0);

    }

    /**
     *Funcion que establece la conexion o arista si una dependencia conecta con un servicio
     */
    public function testConectarSi()
    {
        $conected = false;
        $testConnected = false;

        $dependencia = array(
            'name'=>'Stub1',
            'domain'=>'StubDomain',
            'version'=>'1.2',
            'optional'=>'true',
            'editable'=>'true',
            'bundlename'=>'pepe',
            'type'=>'dependencia',
            'properties'=>array(
                'stubname'=>'string',
                'stubid'=>'integer',
                'stubdate'=>'datetime',
            )

        );

        $servicio  = array(
            'name'=>'Stub1',
            'domain'=>'StubDomain',
            'version'=>'1.2',
            'optional'=>'true',
            'editable'=>'true',
            'bundlename'=>'pepe',
            'type'=>'servicio',
            'uri'=>'http://api/res/stub4',
            'properties'=>array(
                'stubname'=>'string',
                'stubid'=>'integer',
                'stubdate'=>'datetime',
                'stubnumber'=>'integer',
                'stuburi'=>'uri',
                'stubtime'=>'datetime',
            )

        );

        if (array_key_exists('properties',$servicio) && array_key_exists('properties',$dependencia)) {
                $servs = array_keys($servicio['properties']);
                $deps = array_keys($dependencia['properties']);

                $servsValues = array_values($servicio['properties']);
                $depsValues = array_values($dependencia['properties']);


            $intersec = array_intersect($servsValues,$depsValues);
         //   var_dump($intersec);exit;
            $conected = count(array_diff($deps,$servs))==0;
                $testConnected = count($intersec)>=count($depsValues);
        }

        $this->assertTrue($conected);
        $this->assertTrue($testConnected);

    }

    /**
     *Funcion que establece la conexion o arista si una dependencia no conecta con un servicio
     */
    public function testConectarNo()
    {
        $conected = false;
        $testConnected = false;

        $dependencia = array(
            'name'=>'Stub1',
            'domain'=>'StubDomain',
            'version'=>'1.2',
            'optional'=>'true',
            'editable'=>'true',
            'bundlename'=>'pepe',
            'type'=>'dependencia',
            'properties'=>array(
                'stubname'=>'string',
                'stubid'=>'integer',
                'stubdate'=>'datetime',
                'stubimagen'=>'uri'
            )

        );

        $servicio  = array(
            'name'=>'Stub1',
            'domain'=>'StubDomain',
            'version'=>'1.2',
            'optional'=>'true',
            'editable'=>'true',
            'bundlename'=>'pepe',
            'type'=>'servicio',
            'uri'=>'http://api/res/stub4',
            'properties'=>array(
                'stubname'=>'string',
                'stubid'=>'integer',
                'stubdate'=>'datetime',
                'stubnumber'=>'integer',
                'stuburi'=>'uri',
                'stubtime'=>'datetime',
            )

        );

        if (array_key_exists('properties',$servicio) && array_key_exists('properties',$dependencia)) {
            $servs = array_keys($servicio['properties']);
            $deps = array_keys($dependencia['properties']);
            $servsValues = array_values($servicio['properties']);
            $depsValues = array_values($dependencia['properties']);
            $intersec = array_intersect($servsValues,$depsValues);
            $conected = count(array_diff($deps,$servs))==0;
            $testConnected = count($intersec)>=count($depsValues);
        }

        $this->assertFalse($conected&&$testConnected);


    }

    /**
     *Verifica las dependencias
     */
    public function testBusquedaArista()
    {
        $dependencia = array(
            'name'=>'Stub1',
            'domain'=>'StubDomain',
            'version'=>'1.2',
            'optional'=>'true',
            'editable'=>'true',
            'bundlename'=>'pepe',
            'type'=>'dependencia',
            'properties'=>array(
                'stubname'=>'string',
                'stubid'=>'integer',
                'stubdate'=>'datetime',
                'stubimagen'=>'uri'
            )

        );

        $recursos = $this->loadDataGraphArray();
        $this->sortBy('type', $recursos);

        $this->assertEquals('dependencia',$recursos[0]['type']);

    }

    /**
     *Busca el índice de los servicios en el grafo disperso
     */
    public function testServicioIndice()
    {
        $recursos = $this->loadDataGraphArray();
        $this->sortBy('type', $recursos);

        $index = -1;

        foreach ($recursos as $key => $recurso) {
            if ($recurso['type']=="servicio") {
                $index = $key;
                break;
            }
        }

        $this->assertGreaterThan(-1,$index,"Has fallado, pues se ha quedado en ".$index);
    }


    public function testGrafo()
    {
        $recursos = $this->loadDataGraphArray();
        $this->sortBy('type',$recursos);
        $indice = $this->getIndiceServicio();
        $adyacencia = array();
        $pivot = $indice-1;

        while ($pivot>=0) {
            for ($i = $indice; $i < sizeof($recursos); $i++) {
                if (self::Conectar($recursos[$pivot],$recursos[$i])) {
                    array_push($adyacencia,array(
                        $recursos[$pivot],$recursos[$i]
                    ));
                }
            }
            $pivot--;
        }



        $this->assertGreaterThan(0,sizeof($adyacencia));


    }


    public function testfindService()
    {
        //TODO ENCONTRAR LA URL DE UN SERVICIO

        $dependencia = array(
            'name'=>'Stub1',
            'domain'=>'StubDomain',
            'version'=>'1.2',
            'optional'=>'true',
            'editable'=>'true',
            'bundlename'=>'pepe',
            'type'=>'dependencia',
            'properties'=>array(
              array(
                  "stub4name"=>"string",
                     "stub4id"=>"integer",
                     "stub4ci"=>"string",
                     "stub4date"=>"datetime",
                      "stub4uri"=>"uri"
              )
            )

        );
        $recursos = $this->loadDataGraphArray();
        $this->sortBy('type',$recursos);
        $uri = "";
        $indice = $this->getIndiceServicio();
        for ($i = $indice; $i< sizeof($recursos)-1 ;$i++ ) {
            if (self::Conectar($dependencia,$recursos[$i])) {
                $uri = $recursos[$i]['uri'];
                break;
            }
        }

        $this->assertNotEquals("",$uri,'oops');

    }


    /**************************funciones de ayuda******************************/

    /**
     *Devuelve un array servicios y dependencias
     */
    public function loadDataGraphArray()
    {
        $json = $this->loadDefault();

        $normalizedData = json_decode($json,true);
        $keys = array_keys($normalizedData);
        $resources = array();
        foreach ($keys as $single) {
            if (is_array($normalizedData[$single])) {
                $resources=array_merge($resources,$normalizedData[$single]);
            }
        }

        return $resources;
    }


    /**
     * Ordena una coleccion por una funcion de comparacion
     * @param $field
     * @param $array
     * @return bool
     */
    function sortBy($field, &$array)
    {
        usort($array, create_function('$a, $b', '
        $a = $a["' . $field . '"];
        $b = $b["' . $field . '"];

        if($a=="servicio" && $b == "servicio" || $a=="dependencia" && $b == "servicio" )
        {
            return 0;
        }
        elseif ($a=="servicio" && $b == "dependencia" )
        {
           return 1;
        }
        else
            return -1;
    '));

        return true;
    }

    /**
     * Iterative binary search
     *
     * @param int $x The target integer to search
     * @param array $list The sorted array
     * @param array $left First index of the array to be searched
     * @param array $right Last index of the array to be searched
     * @return int The index of the search key if found, otherwise -1
     */
    function binary_search($x, $list) {
        $left = 0;
        $right = count($list) - 1;

        while ($left <= $right) {
            $mid = ($left + $right)/2;

            if ($list[$mid] == $x) {
                return $mid;
            } elseif ($list[$mid] > $x) {
                $right = $mid - 1;
            } elseif ($list[$mid] < $x) {
                $left = $mid + 1;
            }
        }

        return -1;
    }

    /**
     * Determina si se puede conectar o no una dependencia y un servicio
     * @param $dependencia
     * @param $servicio
     * @return bool
     */
    public function Conectar($dependencia, $servicio)
    {
        $conected = false;
        $testConnected = false;

        if ($dependencia['type']==$servicio['type']) {
           return false;
        }

        if (array_key_exists('properties',$servicio) && array_key_exists('properties',$dependencia)) {
           //Comparo las claves
            $servs = array_keys($servicio['properties'][0]);
            $deps = array_keys($dependencia['properties'][0]);
            $depsValues = array_values($dependencia['properties'][0]);

            $intersec = array_intersect_key($dependencia['properties'][0],$servicio['properties'][0]);

            $conected = count(array_diff($deps,$servs))==0;

            $testConnected = count($intersec)>=count($depsValues);

        }

        return $conected&&$testConnected;

    }
    public function getIndiceServicio()
    {
        $recursos = $this->loadDataGraphArray();
        $this->sortBy('type', $recursos);

        $index = -1;

        foreach ($recursos as $key => $recurso) {
            if ($recurso['type']=="servicio") {
                $index = $key;
                break;
            }
        }

        return $index;
    }

    public function getMockGraph()
    {
        $recursos = $this->loadDataGraphArray();
        $this->sortBy('type',$recursos);
        $indice = $this->getIndiceServicio();
        $adyacencia = array();
        $pivot = $indice;

        while ($pivot>=0) {
            for ($i = $indice; $i < sizeof($recursos); $i++) {
                if (self::Conectar($recursos[$pivot],$recursos[$i])) {
                    array_push($adyacencia,array(
                        $recursos[$pivot],$recursos[$i]
                    ));
                }
            }
            $pivot--;
        }

        return $adyacencia;
    }
}
 