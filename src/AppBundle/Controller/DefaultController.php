<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\Registration;
use AppBundle\Entity\SpeedDatingEvent;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
{
    /**
     * Show the homepage
     *
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $events = $this->getDoctrine()->getRepository('AppBundle:Event')
            ->findNext();

        return $this->render('frontend/index.html.twig', array(
            'events' => $events,
        ));
    }

    /**
     * Show a specific event
     *
     * @Route("/event/{id}", name="show_event")
     */
    public function showEventAction(Event $event)
    {
        $isRegistered = false;

        if($event->getParticipants()->contains($this->getUser())) {
            $isRegistered = true;
        }
        return $this->render('frontend/showEvent.html.twig', array(
            'event' => $event,
            'isRegistered' => $isRegistered
        ));
    }

    /**
     * Register to an event via click on link
     *
     * @Route("/registration/new/{id}", name="new_event_registration")
     * @Security("has_role('ROLE_USER')")
     */
    public function registerEventAction(SpeedDatingEvent $event) {
        $user = $this->getUser();
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

            $this->addFlash('error', $errorMessage);
            return $this->redirectToRoute('fos_user_profile_edit');
        }

        // And is not already registered
        if($event->getParticipants()->contains($user)) {
            $errorMessage = 'You are already registered to this event';
        }

        if(!empty($errorMessage)) {
            $this->addFlash('error', $errorMessage);
            return $this->redirectToRoute('show_event', array('id' => $event->getId()));
        }

        // Everyting okay, we can register the User
        if($user->getGender() === 'male') {
            $event->setNumMenRegistered($event->getNumMenRegistered() + 1);
        }
        else {
            $event->setNumWomenRegistered($event->getNumWomenRegistered() + 1);
        }

        $event->addParticipant($user);

        $registration = new Registration($event, $user);

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

        return $this->render('frontend/registrationSuccess.html.twig');
    }

    /**
     * @Route("/registration/delete/{id}", name="delete_event_registration")
     * @Security("has_role('ROLE_USER')")
     */
    /*public function deleteRegistrationEventAction(Registration $registration) {
        $user = $this->getUser();
        $errorMessage = '';

        if($registration === null) {
            throw $this->createNotFoundException('Registration not found');
        }

        $event = $registration->getEvent();

        if($event->getState() !== 'pending') {
            $errorMessage = 'You cannot unregister from a confirmed or cancelled event.';
        }

        if($user !== $registration->getUser()) {
            $errorMessage = 'You do not have permission to delete this registration.';
        }

        if(!empty($errorMessage)) {
            $this->addFlash('error', $errorMessage);
            $this->redirectToRoute('fos_user_profile_show');
        }

        // Everything okay, we can delete
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

        return $this->render('frontend/registrationDelete.html.twig');
    }*/
}
