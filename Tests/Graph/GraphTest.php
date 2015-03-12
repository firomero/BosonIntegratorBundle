<?php


namespace IntegratorBundle\Tests\Graph;
use PlasmaConduit\DependencyGraph;
use PlasmaConduit\dependencygraph\DependencyGraphNode;

class GraphTest extends \PHPUnit_Framework_TestCase{

    /**
     *Probando la Carga de la clase de grafo para las dependencias.
     */
    public function testClassGraph()
    {
        $grafo = new DependencyGraph();
        $class = get_class($grafo);
        $this->assertNotEmpty($class);
    }


    /**
     *Genera el mapa de servicio/dependencia
     */
    public function testGrafo()
    {
        $mapa = new DependencyGraph();
        $recursos = $this->loadDataGraphArray();
        $this->sortBy('type',$recursos);
        $indice = $this->getIndiceServicio();
        $pivot = $indice-1;

        while ($pivot>=0) {
            for ($i = $indice; $i < sizeof($recursos); $i++) {
                if (self::Conectar($recursos[$pivot],$recursos[$i])) {
                    $nodoD = new DependencyGraphNode($recursos[$pivot]['name'].$recursos[$pivot]['domain']);
                    $nodoS = new DependencyGraphNode($recursos[$i]['uri']);
                    $mapa->addRoot($nodoD);
                    $mapa->addDependency($nodoD,$nodoS);
                }
            }
            $pivot--;
        }

        $this->assertGreaterThan(0,sizeof($mapa->toArray()),"Oops it is zero");
    }


    /**
     *Encuentra una dependencia
     */
    public function testFindDependency()
    {
        $mapa = $this->getGrafo();
        $dataArray = $mapa->toArray();
        $flatten = $mapa->flatten();
        $uri = '';
//        foreach ($dataArray as $array) {
//            if (array_key_exists('hisdato:trazas',$array)) {
//                $uri = current($array['hisdato:trazas']);
//            }
//        }

        $hash = array();
        foreach ($dataArray as $array) {
            $llave = array_keys($array);
            $value = array_values($array);
            $hash[current($llave)]=current($value);
        }

        $uri = current($hash['hisdato:trazas']);

        ladybug_dump($hash);

        $expected1 = "http://10.58.10.152:8888/app.php/api/trazas/trazas_rendimiento/{id}";
        $expected2 = 'http://api/rest/stub4';



        $this->assertEquals($expected1,$uri,'Oops this is not what are you looking for');
    }

    public function testBinaryService()
    {
        $recursos = $this->loadDataGraphArray();
        $this->sortBy('type', $recursos);

        $index = -1;
        $index = $this->binary_search($recursos,0,sizeof($recursos),'servicio', 'self::cmp');
        $this->assertGreaterThan(-1,$index,'This is not what are you looking for');


    }


    /******************************AUXILIAR*************************************/

    public function getGrafo()
    {
        $mapa = new DependencyGraph();
        $recursos = $this->loadDataGraphArray();
        $this->sortBy('type',$recursos);
        $indice = $this->getIndiceServicio();
        $pivot = $indice-1;

        while ($pivot>=0) {
            for ($i = $indice; $i < sizeof($recursos); $i++) {
                if (self::Conectar($recursos[$pivot],$recursos[$i])) {
                    $nodoD = new DependencyGraphNode($recursos[$pivot]['name'].':'.$recursos[$pivot]['domain']);
                    $nodoS = new DependencyGraphNode($recursos[$i]['uri']);
                    $mapa->addRoot($nodoD);
                    $mapa->addDependency($nodoD,$nodoS);
                }
            }
            $pivot--;
        }

       return $mapa;
    }



    /**
     *Devuelve un array servicios y dependencias
     */
    public function loadDataGraphArray()
    {
        $json = $this->loadDefault2();
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
                    "stub4name":"string",
                    "stub4id":"integer",
                    "stub4ci":"string",
                    "stub4date":"datetime",
                    "stub4uri":"uri"
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

    public function loadDefault2()
    {
        $wget = '{
    "services": [
        {
            "name": "trazas_rendimiento",
            "type": "servicio",
            "allow": [
                "PATCH",
                "POST"
            ],
            "version": null,
            "domain": "trazas",
            "uri": "http:\/\/10.58.10.152:8888\/app.php\/api\/trazas\/trazas_rendimiento\/{id}",
            "properties": {"idTraza": "integer", "tiempoEjecucion": "float", "memoria": "float"},
            "bundlename": "TrazasBundle"
        },
        {
            "name": "accion",
            "type": "servicio",
            "allow": [
                "PUT",
                "POST"
            ],
            "version": "1.2",
            "domain": "Gestion",
            "uri": "http:\/\/10.58.10.152:8888\/app.php\/api\/Gestion\/accion\/{id}",
            "properties": {
                "idTraza": "integer",
                "fecha": "string",
                "hora": "string",
                "usuario": "string",
                "ipHost": "string",
                "rol": "string",
                "referencia": "string",
                "controlador": "string",
                "accion": "string",
                "inicio": "string",
                "falla": "string"
            },
            "bundlename": "TrazasBundle"
        },
        {
            "name": "patas",
            "type": "servicio",
            "allow": [
                "GET",
                "POST"
            ],
            "version": null,
            "domain": "perrera",
            "uri": "http:\/\/10.58.10.152:8888\/app.php\/api\/perrera\/patas\/{id}",
            "properties": {"id": "integer", "color": "string"},
            "bundlename": "PruebaBundle"
        },
        {
            "name": "perro",
            "type": "servicio",
            "allow": [
                "PUT",
                "GET"
            ],
            "version": null,
            "domain": "perrera",
            "uri": "http:\/\/10.58.10.152:8888\/app.php\/api\/perrera\/perro\/{id}",
            "properties": {"id": "integer", "nombre": "string", "patas": "array"},
            "bundlename": "PruebaBundle"
        }
    ],
    "dependency": [
        {
            "name": "hisdato",
            "type": "dependencia",
            "optional": true,
            "version": null,
            "domain": "trazas",
            "properties": {"idTraza": "integer", "tiempoEjecucion": "float", "memoria": "float"},
            "bundlename": "TrazasBundle"
        },
        {
            "name": "mabulla",
            "type": "dependencia",
            "optional": true,
            "version": null,
            "domain": "trazas",
            "properties": {"idTraza": "integer", "tiempoEjecucion": "float", "memoria": "float"},
            "bundlename": "TrazasBundle"
        },
        {
            "name": "ichinose",
            "type": "dependencia",
            "optional": true,
            "version": null,
            "domain": "trazas",
            "properties": {"idTraza": "integer", "tiempoEjecucion": "float", "memoria": "float"},
            "bundlename": "TrazasBundle"
        }
    ]
}';
        return $wget;
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


            $servs = array_keys($servicio['properties']);
            $deps = array_keys($dependencia['properties']);
            $depsValues = array_values($dependencia['properties']);

            $intersec = array_intersect_key($dependencia['properties'],$servicio['properties']);

            $conected = count(array_diff($deps,$servs))==0;

            $testConnected = count($intersec)>=count($depsValues);

        }

        return $conected&&$testConnected;

    }

    /**
     * Devuelve el índice de servicio
     * @return int|string
     */
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

    /*
     * Parameters:
     *   $a - The sort array.
     *   $first - First index of the array to be searched (inclusive).
     *   $last - Last index of the array to be searched (exclusive).
     *   $key - The key to be searched for.
     *   $compare - A user defined function for comparison. Same definition as the one in usort
     *
     * Return:
     *   index of the search key if found, otherwise return (-insert_index - 1).
     *   insert_index is the index of smallest element that is greater than $key or sizeof($a) if $key
     *   is larger than all elements in the array.
     */
    function binary_search(array $a, $first, $last, $key, $compare) {
        $lo = $first;
        $hi = $last - 1;

        while ($lo <= $hi) {
            $mid = (int)(($hi - $lo) / 2) + $lo;
            $cmp = call_user_func($compare, $a[$mid], $key);

            if ($cmp < 0) {
                $lo = $mid + 1;
            } elseif ($cmp > 0) {
                $hi = $mid - 1;
            } else {
                return $mid;
            }
        }
        return -($lo + 1);
    }


    function cmp($a, $b) {


        if ($a['type']==$b) {
            return 0;
        }
        return -1;
    }





}