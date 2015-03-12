<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/01/15
 * Time: 15:59
 */

namespace UCI\Boson\IntegratorBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\AnnotationException;
use UCI\Boson\IntegratorBundle\Annotation\Interfaces\ServiceAnnotationInterface;

/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *   @Attribute("name", type = "string"),
 *   @Attribute("domain", type="string",required=true),
 *   @Attribute("version", type="string"),
 *   @Attribute("allow",  type = "array<string>")
 * })
 *
 * Class RestServiceAnotation
 *
 * @author Daniel Arturo Casals Amat<dacasals@uci.cu>
 * @package UCI\Boson\IntegratorBundle\Annotation
 */
class RestService  implements ServiceAnnotationInterface {

    public $name;

    public $allow;

    public $domain;

    public $version;

    public $optional = true;

    function __construct( $values )
    {
        $arrayPermitidos = array("PUT","GET","POST","PATCH","DELETE");

        foreach($values['allow'] as $element){
            if($element != "PUT" && $element != "GET" && $element !="POST" &&
                $element !="PATCH" && $element !="DELETE" )
                throw AnnotationException::enumeratorError('allow','RestService','',$arrayPermitidos,$element.array_search($element,$arrayPermitidos));
        }
        foreach($values as $key =>$value)
        {
            if( property_exists($this,$key) )
            {
                $this->$key = $value;
            }
            else throw  AnnotationException::creationError("El atributo ".$key." no está definido para anotaciones de tipo RestService, verifique los atributos permitidos para esta anotación");
        }
    }

//    /**
//     * @return mixed
//     */
//    public function getEditable()
//    {
//        if( count ($this->allow) == 1 && $this->allow[0] == "GET" )
//             $this->editable = false;
//        return $this->editable;
//    }
}