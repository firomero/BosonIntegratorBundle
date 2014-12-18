<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 17/12/2014
 * Time: 13:03
 */

namespace IntegratorBundle\Model;


use Doctrine\Common\Collections\ArrayCollection;

class Dependency {
    protected $name;
    protected $version;
    protected $depends;

    public function getName()
    {
        return $this->name;
    }
    public function getVersion()
        {
            return $this->version;
        }
    public function getDepends()
        {
            return $this->depends;
        }

    public function setName($name)
    {
         $this->name=$name;
        return $this;
    }
public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }
public function setDepends($depends)
    {
        $this->depends = $depends;
        return $this;
    }

    public function __construct()
    {
        $this->name = "default_name";
        $this->version = "dev-master";
        $this->depends = new ArrayCollection();
    }

} 