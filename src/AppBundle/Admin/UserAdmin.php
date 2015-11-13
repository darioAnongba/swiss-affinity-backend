<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24.10.2015
 * Time: 16:21
 */

namespace AppBundle\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\AddressType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class UserAdmin extends Admin
{
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('Données', array('class' => 'col-md-4'))
                ->add('firstName')
                ->add('lastName')
                ->add('email')
                ->add('username')
                ->add('mobilePhone')
                ->add('homePhone')
                ->add('gender')
                ->add('birthDate')
                ->add('profession')
            ->end();
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Personal information', array('class' => 'col-md-3'))
                ->add('email', 'email')
                ->add('username', null, array(
                    'disabled' => true
                ))
                ->add('firstName')
                ->add('lastName')
                ->add('mobilePhone')
                ->add('homePhone')
                ->add('gender', 'choice', array(
                    'choices' => USer::getGenders()
                ))
                ->add('birthdate', 'date', array(
                    'widget' => 'single_text',
                ))
                ->add('profession')
            ->end()
            ->with('Address', array('class' => 'col-md-3'))
                ->add('address', new AddressType(), array(
                    'required' => false
                ))
            ->end()
            ->with('Preferences', array('class' => 'col-md-3'))
                ->add('locationsOfInterest', 'entity', array(
                    'class' => 'AppBundle\Entity\Location',
                    'property' => 'name',
                    'multiple' => true,
                    'required' => false
                ))
                ->add('eventsAttended', 'entity', array(
                    'class' => 'AppBundle\Entity\Event',
                    'property' => 'name',
                    'multiple' => true,
                    'required' => false
                ))
            ->end()
            ->with('Administration', array('class' => 'col-md-3'))
                ->add('categories', 'entity', array(
                    'class' => 'AppBundle\Entity\UserCategory',
                    'property' => 'name',
                    'multiple' => true,
                    'required' => false
                ))
                ->add('roles', 'collection', array(
                    'type' => 'choice',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'options' => array(
                        'label' => false,
                        'choices' => array(
                            'ROLE_ADMIN' => 'Admin',
                            'ROLE_USER' => 'Standard User'
                        )
                    )
                ))
                ->add('facebookId', 'text')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('firstName')
            ->add('lastName')
            ->add('categories')
            ->add('locationsOfInterest')
            ->add('gender', null, array(), 'choice', array(
                'choices' => User::getGenders()
            ));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('username')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('mobilePhone')
            ->add('gender')
            ->add('birthDate')
            ->add('locationsOfInterest')
            ->add('categories')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'delete' => array()
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof User
            ? $object->getFirstName(). ' '.$object->getLastName()
            : 'Nouvel utilisateur'; // shown in the breadcrumb on the create view
    }
}