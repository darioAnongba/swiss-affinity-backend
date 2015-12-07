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
use DateTime;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegistrationController extends FOSRestController
{
    /**
     * List all registrations for a User.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when user not found"
     *   }
     * )
     *
     * @param string $username The User username
     *
     * @return View
     */
    public function getUserRegistrationsAction($username)
    {
        $user = $this->get('fos_user.user_manager')->findUserByUsername($username);

        if (null === $user) {
            throw $this->createNotFoundException("User does not exist.");
        }

        $registrations = $this->getDoctrine()->getRepository('AppBundle:Registration')
            ->findBy(array('user' => $user));

        return new View($registrations);
    }

    /**
     * Get a single registration.
     *
     * @ApiDoc(
     *   output="AppBundle\Entity\Registration",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the registration is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="registration")
     *
     * @param int $id The registration id
     *
     * @return View
     * @throws NotFoundHttpException when the event does not exist
     */
    public function getRegistrationAction($id)
    {
        $registration = $this->getDoctrine()->getRepository('AppBundle:Registration')->find($id);

        if (null === $registration) {
            throw $this->createNotFoundException("Registration does not exist.");
        }

        return new View($registration);
    }

    /**
     * Presents the form to use to create a new Registration.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @return FormTypeInterface
     */
    public function newRegistrationAction()
    {
        return $this->createForm(new EventRegistrationType());
    }

    /**
     * Creates a new registration from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\EventRegistrationType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when user or event not found",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface[]|View
     */
    public function postUserRegistrationsAction(Request $request)
    {
        $registration = new Registration();

        $form = $this->createForm(new EventRegistrationType(), $registration);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $username = $form["username"]->getData();
            $eventId = $form["eventId"]->getData();

            $user = $this->getDoctrine()->getRepository("AppBundle:User")->findOneBy(array('username' => $username));
            $event = $this->getDoctrine()->getRepository("AppBundle:Event")->find($eventId);

            if($user === null OR $event === null) {
                throw $this->createNotFoundException('Event or User not found');
            }

            $errorMessage = '';

            // Event has to be pending
            if($event->getState() !== 'pending') {
                $errorMessage = 'The event is already confirmed or cancelled';
            }

            // Event has to be in the future
            if($event->getDateStart() <= new DateTime('now')) {
                $errorMessage = 'The event has already passed';
            }

            // And seats available
            if(
                ($user->getGender() === 'male' && $event->getNumMenRegistered() === $event->getMenSeats()) OR
                ($user->getGender() === 'female' && $event->getNumWomenRegistered() === $event->getWomenSeats())
            ) {
                $errorMessage = 'No more seats available for this event';
            }

            // And the user in the age range
            $age = DateTime::createFromFormat('d/m/Y', $user->getBirthDate()->format('d/m/Y'))
                ->diff(new DateTime('now'))
                ->y;

            if($age < $event->getMinAge() || $age > $event->getMaxAge()) {
                $errorMessage = 'You are not in the age range of this Event. The age range is: '
                    .$event->getMinAge().' - '.$event->getMaxAge().' and you are '.$age;
            }

            // And has to enter his address
            if($user->getAddress() === null) {
                $errorMessage = 'Please enter your address before registering to an event';
            }

            // And is not already registered
            if($event->getParticipants()->contains($user)) {
                $errorMessage = 'You are already registered to this event';
            }

            if(!empty($errorMessage)) {
                return new View($errorMessage, 400);
            }

            // Everyting okay, we can register the User
            if($user->getGender() === 'male') {
                $event->setNumMenRegistered($event->getNumMenRegistered() + 1);
            }
            else {
                $event->setNumWomenRegistered($event->getNumWomenRegistered() + 1);
            }

            $event->addParticipant($user);

            $registration->setEvent($event);
            $registration->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($registration);
            $em->flush();

            // We send the Email
            $message = \Swift_Message::newInstance()
                ->setSubject('Swiss Affinity - Event Registration')
                ->setFrom('no-reply@swissaffinity.dev')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/registration.html.twig'
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            return new View(null, 204);
        }

        return new View(array('form' => $form));
    }

    /**
     * Removes a registration.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     400="Returned when an error occured",
     *     404="Returned when registration not found",
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param string     $id      the registration id
     *
     * @return View
     *
     * @throws NotFoundHttpException when registration does not exist
     */
    public function deleteRegistrationsAction($id)
    {
        $registration = $this->getDoctrine()->getRepository('AppBundle:Registration')->find($id);
        $errorMessage = '';

        if($registration === null) {
            throw $this->createNotFoundException('Registration not found');
        }

        $event = $registration->getEvent();

        if($event->getState() !== 'pending') {
            $errorMessage = 'You cannot unregister from a confirmed or cancelled event.';
        }

        if(!empty($errorMessage)) {
            $view = new View($errorMessage);
            $view->setStatusCode(400);

            return $view;
        }

        // Everything okay, we can delete
        $user = $registration->getUser();

        $event->removeParticipant($user);

        if($user->getGender() === 'male') {
            $event->setNumMenRegistered($event->getNumMenRegistered() - 1);
        }
        else {
            $event->setNumWomenRegistered($event->getNumWomenRegistered() - 1);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($registration);
        $em->flush();
    }
}