<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24.10.2015
 * Time: 21:09
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends FOSRestController
{
    /**
     * List all users.
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
    public function getUsersAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        return $users;
    }

    /**
     * Get a single user.
     *
     * @ApiDoc(
     *   output="AppBundle\Entity\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="user")
     *
     * @param $username
     * @return array
     *
     * @throws NotFoundHttpException when event does not exist
     */
    public function getUserAction($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if (null == $user) {
            throw $this->createNotFoundException("User does not exist.");
        }

        $view = new View($user);
        return $view;
    }

    /**
     * Presents the form to use to create a new User.
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
     * @return FormTypeInterface
     */
    public function newUserAction()
    {
        return $this->createForm(new RegistrationType());
    }

    /**
     * Creates a new user from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\RegistrationType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template = "AppBundle:User:newUser.html.twig",
     *   statusCode = Response::HTTP_BAD_REQUEST
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface[]|View
     */
    public function postUsersAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        /**
         * @var $user User
         */
        $user = $userManager->createUser();

        $form = $this->createForm(new RegistrationType(), $user);

        $form->submit($request);
        if ($form->isValid()) {

            $userManager->updateUser($user);

            return $this->routeRedirectView('get_user', array('id' => $user->getUsername()));
        }

        return array(
            'form' => $form
        );
    }

    /**
     * Presents the form to use to update an existing user.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Annotations\View()
     *
     * @param Request $request the request object
     * @param string     $username      the user username
     *
     * @return FormTypeInterface
     *
     * @throws NotFoundHttpException when note not exist
     */
    public function editUsersAction(Request $request, $username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if (null == $user) {
            throw $this->createNotFoundException("User does not exist.");
        }

        $form = $this->createForm('app_user_profile', $user);

        return $form;
    }

    /**
     * Update existing user from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\ProfileFormType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *   template="AppBundle:User:editUser.html.twig",
     *   templateVar="form"
     * )
     *
     * @param Request $request the request object
     * @param string     $username      the user username
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function putUsersAction(Request $request, $username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if (null == $user) {
            throw $this->createNotFoundException("User does not exist.");
        } else {
            $statusCode = Response::HTTP_NO_CONTENT;
        }

        $form = $this->createForm('app_user_profile', $user);
        $form->submit($request);

        if ($form->isValid()) {
            $userManager->updateUser($user);

            return $this->routeRedirectView('get_user', array('username' => $user->getUsername()), $statusCode);
        }

        return $form;
    }

    /**
     * Removes a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param string     $username      the user username
     *
     * @return View
     */
    public function deleteUsersAction($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        $userManager->deleteUser($user);

        return $this->routeRedirectView('get_users', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * List all locations of a User.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="locations")
     *
     * @param integer $username   The user's username
     * @return array
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function getUserLocationsAction($username)
    {
        $user = $this->get('fos_user.user_manager')->findUserByUsername($username);

        if(null === $user) throw $this->createNotFoundException("User not found");

        $view = new View($user->getLocationsOfInterest());
        return $view;
    }
}