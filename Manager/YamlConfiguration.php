<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 20/02/15
 * Time: 10:28
 */

namespace UCI\Boson\IntegratorBundle\Manager;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Yaml\Yaml;
use UCI\Boson\IntegratorBundle\Exception\IntegratorException;

/**
 * Lector de configuracion yml
 * Class YamlConfiguration
 * @package UCI\Boson\IntegratorBundle\Manager
 */
class YamlConfiguration implements  ConfigurationManagerInteface{

    private  $translator;
    function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }


    /**
     * Este metodo es el que carga un contenido desde la fuente de quien la implemente y devuelve una coleccion con sus valores.
     * @param null $file
     * @throws IntegratorException
     * @return mixed
     */
    public function fileAsArray($file=null)
    {
        if (file_exists($file)) {
            $data = Yaml::parse(file_get_contents($file));
            return $data;
        }

        throw new \Exception('file not found');
    }

    /**
     * Este método es el encargado de recibir un array y traducirlo a su fuente original
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
