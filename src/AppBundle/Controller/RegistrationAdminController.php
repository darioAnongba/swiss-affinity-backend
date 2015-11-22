<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 21.11.2015
 * Time: 22:51
 */

namespace AppBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegistrationAdminController extends CRUDController
{
    public function deleteAction($id, Request $request = null)
    {
        $object  = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        if ($this->getRestMethod() === 'DELETE') {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete');
            $objectName = $this->admin->toString($object);
            try {
                $event = $object->getEvent();
                $user = $object->getUser();

                // Everything okay, we can delete
                $event->removeParticipant($user);

                if($user->getGender() === 'male') {
                    $event->setNumMenRegistered($event->getNumMenRegistered() - 1);
                }
                else {
                    $event->setNumWomenRegistered($event->getNumWomenRegistered() - 1);
                }

                $this->admin->delete($object);
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array('result' => 'ok'), 200, array());
                }
                $this->addFlash(
                    'sonata_flash_success',
                    $this->admin->trans(
                        'flash_delete_success',
                        array('%name%' => $this->escapeHtml($objectName)),
                        'SonataAdminBundle'
                    )
                );
            } catch (ModelManagerException $e) {
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array('result' => 'error'), 200, array());
                }
                $this->addFlash(
                    'sonata_flash_error',
                    $this->admin->trans(
                        'flash_delete_error',
                        array('%name%' => $this->escapeHtml($objectName)),
                        'SonataAdminBundle'
                    )
                );
            }
            return $this->redirectTo($object);
        }
        return $this->render($this->admin->getTemplate('delete'), array(
            'object'     => $object,
            'action'     => 'delete',
            'csrf_token' => $this->getCsrfToken('sonata.delete'),
        ), null);
    }

    public function confirmAction($id)
    {
        $registration = $this->admin->getSubject();

        if (!$registration) {
            throw new NotFoundHttpException(sprintf('unable to find the registration'));
        }

        $registration->setState('confirmed');

        $this->admin->update($registration);

        $user = $registration->getUser();
        // We send the Email
        $message = \Swift_Message::newInstance()
            ->setSubject('Swiss Affinity - Event Confirmation')
            ->setFrom('no-reply@swissaffinity.dev')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registrationConfirmation.html.twig'
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $this->addFlash('sonata_flash_success', 'Confirmed successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    public function cancelAction($id)
    {
        $registration = $this->admin->getSubject();

        if (!$registration) {
            throw new NotFoundHttpException(sprintf('unable to find the registration'));
        }

        $registration->setState('cancelled');

        $this->admin->update($registration);

        $user = $registration->getUser();
        // We send the Email
        $message = \Swift_Message::newInstance()
            ->setSubject('Swiss Affinity - Event registration denied')
            ->setFrom('no-reply@swissaffinity.dev')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registrationDenied.html.twig'
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $this->addFlash('sonata_flash_success', 'Cancelled successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}