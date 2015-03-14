<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 28/12/2014
 * Time: 20:04
 */

namespace IntegratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class DependencyController extends Controller{

    /**
     * Get single Page.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Dependency for a given name",
     *   output = "Boson\IntegratorBundle\Model\Dependency",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the page is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="page")
     *
     * @return array
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function getDependency()
    {
        $manager = $this->get('integrator.dependency.manager');
        $list =$manager->get();
        return $list;

    }
} 