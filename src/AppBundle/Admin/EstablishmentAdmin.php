<?php
    /**
     * Created by PhpStorm.
     * User: dario
     * Date: 05.11.2015
     * Time: 16:21
     */

namespace AppBundle\Admin;

use AppBundle\Entity\Establishment;
use AppBundle\Entity\Event;
use AppBundle\Form\AddressType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EstablishmentAdmin extends Admin
{
    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Details', array('class' => 'col-md-4'))
                ->add('name')
                ->add('type', 'choice', array(
                    'choices' => Establishment::getTypes(),
                    'placeholder' => 'Choose from the list'
                ))
                ->add('phoneNumber')
                ->add('url')
                ->add('maxSeats')
                ->add('location')
            ->end()
            ->with('Address', array('class' => 'col-md-4'))
                ->add('address', new AddressType(), array(
                    'label' => false
                ))
            ->end()
            ->with('Details 2', array('class' => 'col-md-4'))
                ->add('logoFile', 'vich_image', array(
                    'required' => false,
                    'allow_delete' => true,
                    'download_link' => false,
                ))
                ->add('description')
            ->end();
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('location')
            ->add('type', null, array(), 'choice', array(
                'choices' => Establishment::getTypes(),
            ));
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->add('location.name')
            ->add('phoneNumber')
            ->add('type')
            ->add('maxSeats')
            ->add('address')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'delete' => array()
                )
            ));
    }

    /**
     * @inheritdoc
     */
    public function toString($object)
    {
        return $object instanceof Event
            ? $object->getName()
            : 'New establishment'; // shown in the breadcrumb on the create view
    }
}