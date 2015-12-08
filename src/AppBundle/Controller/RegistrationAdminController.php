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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegistrationAdminController extends CRUDController
{
    /**
     * Delete a registration
     *
     * @param int|null|string $id
     * @param Request|null $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
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
}