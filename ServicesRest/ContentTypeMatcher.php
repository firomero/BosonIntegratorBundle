<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 20/03/15
 * Time: 10:20
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;


/**
 * Resposabilidad de determinar el tipo de encoder de las respuestas http
 * Class ContentTypeMatcher
 * @package UCI\Boson\IntegratorBundle\ServicesRest
 */
trait ContentTypeMatcher {

    public function match($contentType)
    {
        $types = array(
            'application/json'=>ContentEncoder::JSON_ENCODER,
            'application/xml'=>ContentEncoder::XML_ENCODER,
            'text/plain'=>ContentEncoder::TEXT_ENCODER,
            'text/html'=>ContentEncoder::HTML_ENCODER,
        );

        return $types[$contentType];
    }
} 