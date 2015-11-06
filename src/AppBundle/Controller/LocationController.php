<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24.10.2015
 * Time: 21:09
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Location;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationController extends FOSRestController
{
    /**
     * List all locations sorted by name.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @return array
     */
    public function getLocationsAction()
    {
        $locations = $this->getDoctrine()->getRepository('AppBundle:Location')
            ->findBy(array(), array('name' => 'ASC'));

        return $locations;
    }

    /**
     * Get all events from a given location sorted by date start.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     200 = "Returned when successful",
     *     404 = "Returned when the location is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="events")
     *
     * @param integer $id   The location's id
     * @return array
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function getLocationsEventsAction($id)
    {
        $location = $this->getDoctrine()->getRepository('AppBundle:Location')->find($id);

        if(null === $location) throw $this->createNotFoundException("Location not found");

        $events = $this->getDoctrine()->getRepository('AppBundle:Event')
            ->findBy(array('location' => $location, "state" => "Pending"), array('dateStart' => 'DESC'));

        $view = new View($events);

        return $view;
    }
}