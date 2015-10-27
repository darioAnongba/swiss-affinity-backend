<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24.10.2015
 * Time: 21:09
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Registration;
use AppBundle\Form\EventRegistrationType;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegistrationController extends FOSRestController
{
    /**
     * List all registrations for a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="id", requirements="\d+", description="Id of the user")
     *
     * @Annotations\View()
     *
     * @return array
     */
    public function getRegistrationsAction(ParamFetcherInterface $paramFetcher)
    {
        $id = $paramFetcher->get('id');

        $registrations = $this->getDoctrine()->getRepository('AppBundle:Registration')
            ->findBy(array('user' => $id), array('date' => 'DESC'));

        return $registrations;
    }

    /**
     * Get a single registration.
     *
     * @ApiDoc(
     *   output="AppBundle\Entity\Registration",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the registation is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="registration")
     *
     * @param $id
     * @return array
     *
     * @throws NotFoundHttpException when a registration does not exist
     */
    public function getRegistrationAction($id)
    {
        $registration = $this->getDoctrine()->getRepository('AppBundle:Registration')->find($id);

        if (null === $registration) {
            throw $this->createNotFoundException("Registration does not exist.");
        }

        $view = new View($registration);

        return $view;
    }

    /**
     * Creates a new registration from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\EventRegistrationType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template = "AppBundle:Registration:newRegistration.html.twig",
     *   statusCode = Response::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface[]|View
     */
    public function postRegistrationsAction(Request $request)
    {
        $registration = new Registration();

        $form = $this->createForm(new EventRegistrationType(), $registration);

        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($registration);
            $em->flush();

            return $this->routeRedirectView('get_registration', array('id' => $registration->getId()));
        }

        return array(
            'form' => $form
        );
    }

    /**
     * Update existing registration from the submitted data or create a new registration.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\EventRegistrationType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template="AppBundle:User:editRegistration.html.twig",
     *   templateVar="form"
     * )
     *
     * @param Request $request the request object
     * @param string     $id      the registration id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when note not exist
     */
    public function putRegistrationsAction(Request $request, $id)
    {
        $registration = $this->getDoctrine()->getRepository('AppBundle:Registration')->find($id);

        if (null === $registration) {
            $registration = new Registration();
            $statusCode = Response::HTTP_CREATED;
        } else {
            $statusCode = Response::HTTP_NO_CONTENT;
        }

        $form = $this->createForm(new EventRegistrationType(), $registration);

        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($registration);
            $em->flush();

            return $this->routeRedirectView('get_registration', array('id' => $registration->getId()), $statusCode);
        }

        return $form;
    }

    /**
     * Removes a registration.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param string     $id      the registration id
     *
     * @return View
     *
     * @throws NotFoundHttpException when registration not exist
     */
    public function deleteRegistrationsAction($id)
    {
        $registration = $this->getDoctrine()->getRepository('AppBundle:Registration')->find($id);
        if (null === $registration) {
            throw $this->createNotFoundException("Registration does not exist.");
        }

        $this->getDoctrine()->getManager()->remove($registration);

        return $this->routeRedirectView('get_users', array(), Response::HTTP_NO_CONTENT);
    }
}