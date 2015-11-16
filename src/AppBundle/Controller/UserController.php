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
use AppBundle\Form\RESTRegistrationFacebookType;
use AppBundle\Form\RESTRegistrationType;
use AppBundle\Form\TestType;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Exporter\Exception\InvalidDataFormatException;
use Fixtures\Bundles\AnnotationsBundle\Entity\Test;
use FOS\RestBundle\EventListener\ParamFetcherListener;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

use Symfony\Component\Config\Definition\Exception\Exception;
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

        return new View($users);
    }

    /**
     * Get a single user by Facebook id
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
     * @param $id
     * @return array
     *
     * @throws NotFoundHttpException when user does not exist
     */
    public function getUserAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repo->findOneBy(array('facebookId' => $id));

        if (null == $user) {
            throw $this->createNotFoundException("User does not exist.");
        }

        return new View($user);
    }

    /**
     * Presents the form to use to create a new User.
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
    public function newUserAction()
    {
        return $this->createForm(new RESTRegistrationType());
    }

    /**
     * Creates a new user from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\RESTRegistrationType",
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
        $user = $this->get('fos_user.user_manager')->createUser();

        $form = $this->createForm(new RESTRegistrationType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $locations = $this->getDoctrine()->getRepository('AppBundle:Location')->findAll();

            foreach($locations as $location) {
                $user->addLocationsOfInterest($location);
            }

            $this->get('fos_user.user_manager')->updateUser($user);

            return $user;
        }

        return new View(array('form' => $form));
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
        }

        $form = $this->createForm('app_user_profile', $user);
        $form->submit($request);

        if ($form->isValid()) {
            $userManager->updateUser($user);

            return $this->routeRedirectView('get_user', array('username' => $user->getUsername()), Response::HTTP_NO_CONTENT);
        }

        return $form;
    }

    /**
     * Removes a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     404 = "Returned when the user is not found"
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

        if (null === $user) {
            throw $this->createNotFoundException("User does not exist.");
        }

        $userManager->deleteUser($user);

        return $this->routeRedirectView('get_users', array(), Response::HTTP_NO_CONTENT);
    }

    /**
     * List all locations of interest for a User.
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

        return new View($user->getLocationsOfInterest());
    }

    /**
     * List all events a User has attented (Registrations that were confirmed).
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
    public function getUserEventsAction($username)
    {
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if(null === $user) throw $this->createNotFoundException("User not found");

        return new View($user->getEventsAttended());
    }
}