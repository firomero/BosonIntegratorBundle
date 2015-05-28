<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 21/05/15
 * Time: 15:29
 */

namespace UCI\Boson\IntegratorBundle\Security;



use UCI\Boson\IntegratorBundle\Security\IntegratorSecurityLoader;
use Symfony\Component\Yaml\Yaml;

class YamlSecurityLoader implements  IntegratorSecurityLoader{


    /**
     *
     * @var array
     */
    protected $config;

    public function __construct($config = array())
    {
        $this->config = $config;
    }

    /**
     * Devuelve un array con las configuraciones a seguir para la autenticar aplicaciones
     * @throws \Exception
     * @return mixed
     */
    public function read()
    {
        if (file_exists($this->config['uri'])) {
            $data = Yaml::parse(file_get_contents($this->config['uri']));
            return $data;
        }

        throw new \Exception('integrator.file.not.found');
    }

    /**
     * Guarda las configuraciones de acceso para servicios no publicos
     * @param array $dataConfig
     * @return mixed
     */
    public function write($dataConfig = array())
    {
        $dump = Yaml::dump($dataConfig);
        file_put_contents($this->config['uri'],$dump);
    }
}