<?php

namespace UCI\Boson\IntegratorBundle\Manager;
use PlasmaConduit\DependencyGraph;
use PlasmaConduit\dependencygraph\DependencyGraphNode;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use UCI\Boson\CacheBundle\Cache\Cache;
use UCI\Boson\ExcepcionesBundle\Exception\LocalException;

class ServiceManager {

    const DEPENDENCY = "dependencia";
    const SERVICE = "servicio";
    const INDEX_SEARCH_TYPE = "type";
    const INDEX_SEARCH_DOMAIN = "domain";
    const GRAPH_DEPENDENCY_ID = "boson.integrator.graph";
    const COLECCTION_DEPENDENCY_ID = "boson.integrator.colecction";
    const DOMAIN_DEPENDENCY_TREE = "boson.integrator.domain.tree";
    const DOMAIN_DEPENDENCY_CONECTED = "boson.integrator.domain.conected";
    const DOMAIN_DEPENDENCY_UNRESOLVED = "boson.integrator.domain.unresolved";

    /**
     * @var bool
     */
    protected  $sensitive;

    /**
     * @var Cache
     */
    protected $cache;



    protected $eventDispatcher;

    public  function __construct(Cache $cache, $sensitive=false)
    {
        $this->sensitive = $sensitive;


        $this->cache = $cache;

    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }
    /**
     * Devuelve normalizada las representaciones de dependencias y servicios
     * @param string $json
     * @return array
     */
    public function Representaciones($json)
    {
        $normalizedData = json_decode($json,true);
        $keys = array_keys($normalizedData);
        $resources = array();
        foreach ($keys as $single) {
            if (is_array($normalizedData[$single])) {
                $resources=array_merge($resources,$normalizedData[$single]);
            }
        }

        //TODO En este punto en vez de ordenar se puede realizar una tecnica de agrupamiento, de manera que se construya
        //TODO un arbol jerarquico de dominio, lo que disminuye el tiempo de busqueda cuando se quiere construir el; grafo
        //TODO La complejidad actual en la construccion del grafo, en el peor de los casos es O(n^2), ya que simula una
        //TODO busqueda binaria.
        $this->sortBy(self::INDEX_SEARCH_TYPE,$resources);

        $this->cache->save(self::COLECCTION_DEPENDENCY_ID,$resources);

        return $resources;
    }

    /**
     * Devuelve normalizada las representaciones de dependencias y servicios
     * @param array $resources     *
     * @return array
     */
    public function RepresentacionesColeccion(array $resources)
    {

        //TODO En este punto en vez de ordenar se puede realizar una tecnica de agrupamiento, de manera que se construya
        //TODO un arbol jerarquico de dominio, lo que disminuye el tiempo de busqueda cuando se quiere construir el; grafo
        //TODO La complejidad actual en la construccion del grafo, en el peor de los casos es O(n^2), ya que simula una
        //TODO busqueda binaria.
        $this->sortBy(self::INDEX_SEARCH_TYPE,$resources);

        $this->cache->save(self::COLECCTION_DEPENDENCY_ID,$resources);

        return $resources;
    }


    /**
     * Registra el grafo de dependencias con las aplicaciones identificadas.
     * @param array $recursos
     * @return DependencyGraph
     */
    public function buildDependecyGraph(array $recursos)
    {
        return $this->initGraph($recursos);

    }

    /**
     * Utilizado para relocalizar una dependencia
     * @param $dependencia
     * @return bool
     */
    public function doMatch($dependencia)
    {
       /**@var DependencyGraph $mapa*/
        $mapa = $this->cache->fetch($this::GRAPH_DEPENDENCY_ID);
        $recursos = $this->cache->fetch($this::COLECCTION_DEPENDENCY_ID);
        $indice = $this->getIndiceServicio($recursos);

        for ($i = $indice; $i < sizeof($recursos); $i++) {
            if (self::Conectar($dependencia,$recursos[$i])) {
                $nodoD = new DependencyGraphNode($dependencia['name'].':'.$dependencia['domain']);
                $nodoS = new DependencyGraphNode($recursos[$i]['uri']);
                $mapa->addRoot($nodoD);
                $mapa->addDependency($nodoD,$nodoS);
                $this->cache->save(self::GRAPH_DEPENDENCY_ID,$mapa);
                return true;
            }
        }

        return false;
    }

    /**
     * Inicializa el grafo de dependencias.
     * @param array $recursos
     * @return DependencyGraph
     */
    public function initGraph($recursos = array())
    {
        $mapa = new DependencyGraph();
        $indice = $this->getIndiceServicio($recursos);
        $pivot = $indice-1;
        $conected = array();

        while ($pivot>=0) {
            for ($i = $indice; $i < sizeof($recursos); $i++) {
                if (self::Conectar($recursos[$pivot],$recursos[$i])) {
                    $nodoD = new DependencyGraphNode($recursos[$pivot]['name'].':'.$recursos[$pivot]['domain']);
                    $nodoS = new DependencyGraphNode($recursos[$i]['uri']);
                    $mapa->addRoot($nodoD);
                    $mapa->addDependency($nodoD,$nodoS);
                    array_push($conected,$recursos[$pivot]);
                }
            }
            $pivot--;
        }

        $this->cache->save(self::GRAPH_DEPENDENCY_ID,$mapa);
        $this->cache->save(self::DOMAIN_DEPENDENCY_UNRESOLVED,array_diff_assoc($recursos,$conected));

        return $mapa;
    }

    /**
     * Dado una dependencia, devuelve el servicio que la solventa
     * @param $name
     * @return mixed|string
     * @throws \Exception
     */
    public function getServicio($name)
    {
        $uri = '';
        $doMatch = false;
        if ($this->cache->contains($this::GRAPH_DEPENDENCY_ID)) {
            $mapa = $this->cache->fetch($this::GRAPH_DEPENDENCY_ID);
            /**
             *@var DependencyGraph $mapa
             * */

            $mapaArray = $this->Normalize($mapa->toArray());

            do{
                $uri = current($mapaArray[$name['name'].':'.$name['domain']]);
                $doMatch = $this->doMatch($name);
            }while(empty($uri)&&$doMatch==true);
        }
        else
        {
            throw new LocalException('E3');
        }

        return $uri;

    }

    /*********************************************PROTECTED**************************************************/

    /**
     * Devuelve el índice de servicio
     * @return int|string
     */
    public  function getIndiceServicio($recursos=array())
    {
       $index = -1;
        foreach ($recursos as $key => $recurso) {
            if (array_key_exists(self::INDEX_SEARCH_TYPE,$recurso)) {
                if ($recurso[self::INDEX_SEARCH_TYPE]==self::SERVICE) {
                    $index = $key;
                    break;
                }
            }
        }

        return $index;
    }

    /**
     * Ordena una coleccion por una funcion de comparacion
     * @param $field
     * @param $array
     * @return bool
     */
   protected function sortBy($field, &$array)
    {
        usort($array, create_function('$a, $b', '
        $a = $a["' . $field . '"];
        $b = $b["' . $field . '"];

        if($a=="'.self::SERVICE.'" && $b == "'.self::SERVICE.'" || $a=="'.self::DEPENDENCY.'" && $b == "'.self::SERVICE.'" )
        {
            return 0;
        }
        elseif ($a=="'.self::SERVICE.'" && $b == "'.self::DEPENDENCY.'" )
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

        //Comparo los tipos de recursos
        if ($dependencia[self::INDEX_SEARCH_TYPE]==$servicio[self::INDEX_SEARCH_TYPE]) {
            return false;
        }


        //Comparo que pertenezcan a un mismo dominio
        if (!self::MatchDomain($dependencia,$servicio)) {
            return false;
        }

        //Comparo sus atributos
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
     * Verifica si los dominios coinciden
     * @param $dependencia
     * @param $servicio
     * @return bool
     */
    protected function MatchDomain($dependencia, $servicio)
    {
        $depD = $dependencia[self::INDEX_SEARCH_DOMAIN];
        $serD = $servicio[self::INDEX_SEARCH_DOMAIN];
        $depD = trim(strtolower($depD));
        $serD = trim(strtolower($serD));

        if(!$this->sensitive){
            return $depD==$serD;
        }

        $lev = levenshtein($depD, $serD);
        return $lev <= strlen($depD) / 3 ;
    }

    /**
     * Normaliza el mapa resultante
     * @param array $dataArray
     * @return array
     */
    protected function Normalize(array $dataArray)
    {
        $hash = array();
        foreach ($dataArray as $array) {
            $llave = array_keys($array);
            $value = array_values($array);
            $hash[current($llave)]=current($value);
        }

        return $hash;
    }



} 