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
     * List all locations.
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
     * @param ParamFetcherInterface $paramFetcher
     * @return array
     */
    public function getLocationsAction(ParamFetcherInterface $paramFetcher)
    {
        $locations = $this->getDoctrine()
            ->getRepository('AppBundle:Location')
            ->findBy(array(), array('name' => 'DESC'));

        return $locations;
    }

    /**
     * Get a single event.
     *
     * @ApiDoc(
     *   output="AppBundle\Entity\Event",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the event is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="event")
     *
     * @param int $id The event id
     *
     * @return array
     *
     * @throws NotFoundHttpException when the event does not exist
     */
    public function getEventAction($id)
    {
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->find($id);

        if (null === $event) {
            throw $this->createNotFoundException("Event does not exist.");
        }

        $view = new View($event);

        return $view;
    }
}