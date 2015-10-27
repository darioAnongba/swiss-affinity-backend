<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24.10.2015
 * Time: 16:21
 */

namespace AppBundle\Admin;

use AppBundle\Entity\Event;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EventAdmin extends Admin
{
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('Données', array('class' => 'col-md-4'))
                ->add('imagePath')
            ->end();
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name')
            ->add('location', 'entity', array(
                'class' => 'AppBundle\Entity\Location',
                'property' => 'name'
            ))
            ->add('maxPeople')
            ->add('dateStart', 'datetime', array(
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ))
            ->add('dateEnd', 'datetime', array(
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ))
            ->add('basePrice')
            ->add('animators', 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'property' => 'fullName',
                'multiple' => true
            ))
            ->add('description')
            ->add('imageFile', 'vich_image', array(
                'required' => false,
                'allow_delete' => true,
                'download_link' => false,
            ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name')
            ->add('location', null, array(), 'entity', array(
                'class' => 'AppBundle\Entity\Location',
                'property' => 'name'
            ))
            ->add('dateStart')
            ->add('animators', null, array(), 'entity', array(
                'class' => 'AppBundle\Entity\User',
                'property' => 'fullName'
            ))
            ->add('state', null, array(), 'choice', array(
                'choices' => Event::getStates(),
            ));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->add('location.name')
            ->add('maxPeople')
            ->add('dateStart')
            ->add('dateEnd')
            ->add('basePrice')
            ->add('animators')
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
            : 'Nouvel évènement'; // shown in the breadcrumb on the create view
    }
}