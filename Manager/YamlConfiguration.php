<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 20/02/15
 * Time: 10:28
 */

namespace UCI\Boson\IntegratorBundle\Manager;
use Symfony\Component\Yaml\Yaml;
use UCI\Boson\ExcepcionesBundle\Exception\LocalException;

class YamlConfiguration implements  ConfigurationManagerInteface{


    /**
     * Este metodo es el que carga un contenido desde la fuente de quien la implemente y devuelve una coleccion con sus valores.
     * @param null $file
     * @throws LocalException
     * @return mixed
     */
    public function fileAsArray($file=null)
    {
        if (file_exists($file)) {
            $data = Yaml::parse(file_get_contents($file));
            return $data;
        }
        throw new LocalException('E6');
    }

    /**
     * Este metodo es el encargado de recibir un array y traducirlo a su fuente original
     * @param array $array
     * @param null $uri
     * @return mixed
     */
    public function arrayAsFile(array $array, $uri=null)
    {
        $dump = Yaml::dump($array);
        file_put_contents($uri,$dump);
    }
}
