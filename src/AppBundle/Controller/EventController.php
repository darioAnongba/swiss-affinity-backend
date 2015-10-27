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

class EventController extends FOSRestController
{
    /**
     * List all events.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing events.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many events to return.")
     *
     * @Annotations\View()
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return array
     */
    public function getEventsAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');

        $events = $this->getDoctrine()
            ->getRepository('AppBundle:Event')
            ->findBy(array(), array('dateStart' => 'DESC'), $limit, $offset);

        return $events;
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