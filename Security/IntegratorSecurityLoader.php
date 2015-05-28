<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 21/05/15
 * Time: 14:51
 */

namespace UCI\Boson\IntegratorBundle\Security;


/**
 * array(
 *  'host' =>nhost,(name)
 *  'port'=> nport,
 *   'remote'=> nremote(ip)
 * 'key' => nkey(compress id)|sha|md5|bcrypt
 * )
 * Interface IntegratorSecurityLoader
 * @package UCI\Boson\IntegratorBundle\Security
 */
interface IntegratorSecurityLoader {

    /**
     * Devuelve un array con las configuraciones a seguir para la autenticar aplicaciones
     * @return mixed
     */
    public function read();

    /**
     * Guarda las configuraciones de acceso para servicios no publicos
     * @param array $dataConfig
     * @return mixed
     */
    public function write($dataConfig = array());
} 