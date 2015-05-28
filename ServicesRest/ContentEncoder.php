<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 19/03/15
 * Time: 16:40
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;


/**
 * Definicion de los content-type aceptados
 * Class ContentEncoder
 * @package UCI\Boson\IntegratorBundle\ServicesRest
 */
class ContentEncoder {

    /**
     *
     */
    const JSON_ENCODER = 'json';
    /**
     *
     */
    const XML_ENCODER = 'xml';
    /**
     *
     */
    const TEXT_ENCODER = 'plain';
    /**
     *
     */
    const YAML_ENCODER = 'yaml';


    const HTML_ENCODER = 'html';
}