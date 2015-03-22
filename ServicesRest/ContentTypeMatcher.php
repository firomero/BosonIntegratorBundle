<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 20/03/15
 * Time: 10:20
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;


trait ContentTypeMatcher {

    public function match($contentType)
    {
        $types = array(
            'application/json'=>ContentEncoder::JSON_ENCODER,
            'application/xml'=>ContentEncoder::XML_ENCODER,
            'text/plain'=>ContentEncoder::TEXT_ENCODER,
        );

        return $types[$contentType];
    }
} 