<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 17/04/15
 * Time: 10:31
 */

namespace UCI\Boson\IntegratorBundle\Entity;

use Symfony\Component\DependencyInjection\Container;
use UCI\Boson\IntegratorBundle\Annotation\RestServiceConsume;
use UCI\Boson\IntegratorBundle\Model\AbstractResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @RestServiceConsume(name="mock",domain="Gestion")
 * @ORM\Table(name="his_mock")
 * @ORM\Entity
 * */
class Mock extends AbstractResource{

    /**
     * @var string
     * @ORM\Column(name="fecha", type="string", length=255, nullable=false)
     */
    public  $fecha;

    /**
     * @var float
     *
     * @ORM\Column(name="id_traza", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="sec_idtraza_seq", allocationSize=1, initialValue=1)
     */
    public $idTraza;

} 