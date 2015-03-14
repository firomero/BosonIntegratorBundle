<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 12/03/15
 * Time: 14:10
 */

namespace IntegratorBundle\Model;


use Symfony\Component\DependencyInjection\Container;
use UCI\Boson\IntegratorBundle\Annotation\RestService;

/*
 * @RestService(name="recurso",domain="api",allow={"GET"})
 * */
class Recurso extends AbstractResource{

    protected $integratorKernel;

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $domain;

    /**
     * @var
     */
    protected $version;

    /**
     * @var
     */
    protected $optional;

    /**
     * @var
     */
    protected $editable;

    /**
     * @var
     */
    protected $bundlename;

    /**
     * @var
     */
    protected $properties;

    protected $serviceContainer;

    public function __construct(Container $container)
    {
        $this->serviceContainer = $container;

        $this->integratorKernel = $container->get('integrator.kernel');
    }

    /**
     * @return mixed
     */
    public function getBundlename()
    {
        return $this->bundlename;
    }

    /**
     * @param mixed $bundlename
     */
    public function setBundlename($bundlename)
    {
        $this->bundlename = $bundlename;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return mixed
     */
    public function getEditable()
    {
        return $this->editable;
    }

    /**
     * @param mixed $editable
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getOptional()
    {
        return $this->optional;
    }

    /**
     * @param mixed $optional
     */
    public function setOptional($optional)
    {
        $this->optional = $optional;
    }

    /**
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties=array())
    {
        $this->properties = $properties;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Aqui el id es un array asociativo de dominio y nombre
     * @param $id
     * @return $this|void
     */
    public function get($id)
    {
        if (is_array($id)) {
            return $this->integratorKernel->getURI($id);
        }

        return null;

    }


}