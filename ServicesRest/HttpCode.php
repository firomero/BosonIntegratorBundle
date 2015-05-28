<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 19/03/15
 * Time: 16:30
 */

namespace UCI\Boson\IntegratorBundle\ServicesRest;


/**
 * Mapeo de los codigos http posibles
 * Class HttpCode
 * @package UCI\Boson\IntegratorBundle\ServicesRest
 */
class HttpCode {

    //2XX SUCCESSFUL OPERATIONS
    const HTTP_OK = 200;
    const HTTP_UNOFFICIAL = 201;
    const HTTP_UNOFFICIALI = 202;
    const HTTP_UNOFFICIALIII = 203;
    const HTTP_DELETED = 204;
    const HTTP_RELOAD = 205;
    const HTTP_PARCIAL = 206;

    //3XX REDIRECTIONS
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_MOVED_FOUNDED = 302;
    const HTTP_MOVED_OTHERS = 303;
    const HTTP_UNMODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_MOVED_PARTIALLY = 307;

    //4XX CLIENT ERRORS
    const HTTP_WRONG_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_PAYMENT_FORBIDDEN = 403;
    const HTTP_RESOURCE_NOTFOUND = 404;
    const HTTP_CONFLICT = 409;
    const HTTP_NOT_AVAILABLE = 410;
    const HTTP_PRECONDITION_FAILED = 412;


    //5XX  SERVER ERRORS
    const HTTP_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVER_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_UNSUPPORTED_VERSION = 505;
}