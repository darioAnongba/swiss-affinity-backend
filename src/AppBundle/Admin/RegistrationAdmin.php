<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 05.11.2015
 * Time: 16:21
 */

namespace AppBundle\Admin;

use AppBundle\Entity\Event;
use Proxies\__CG__\AppBundle\Entity\Registration;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class RegistrationAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {}

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('User', array('class' => 'col-md-4'))
            ->add('user', 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'property' => 'fullName'
            ))
            ->end()
            ->with('Event', array('class' => 'col-md-4'))
            ->add('event', 'entity', array(
                'class' => 'AppBundle\Entity\Event',
                'property' => 'name'
            ))
            ->end()
            ->with('Status', array('class' => 'col-md-4'))
            ->add('state', 'choice', array(
                'choices' => Registration::getStates(),
            ))
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user', null, array(), 'entity', array(
                'property' => 'fullName'
            ))
            ->add('event', null, array(), 'entity', array(
                'property' => 'name'
            ))
            ->add('date', null, array(), 'date', array(
                'widget' => 'single_text'
            ))
            ->add('state', null, array(), 'choice', array(
                'choices' => Registration::getStates()
            ));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('user.fullName')
            ->add('event.name')
            ->add('date')
            ->add('state')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'delete' => array()
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof Event
            ? $object->getName()
            : 'New Registration'; // shown in the breadcrumb on the create view
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }
}