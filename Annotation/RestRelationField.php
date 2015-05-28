<?php
/**
 * Created by PhpStorm.
 * User: dacasals
 * Date: 28/01/15
 * Time: 15:59
 */

namespace UCI\Boson\IntegratorBundle\Annotation;

use UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface;

/**
 * @Annotation
 * @Target("PROPERTY")
 *
 * Class RestRelationFieldAnotation.  Anotación que etiqueta los campos de ipermedia de los recursos y la forma en que se deben brindar(OBJECT,HYPERMEDIA,IDENTIFIER)
 *
 * @author Daniel Arturo Casals Amat<dacasals@uci.cu>
 * @package UCI\Boson\IntegratorBundle\Annotation
 */
class RestRelationField  implements ServiceAnnotationInterface {

    /**
     * @Enum({"HYPERMEDIA", "IDENTIFIER", "OBJECT"})
     */
    public $type;

}
