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
    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('Données', array('class' => 'col-md-4'))
                ->add('name')
                ->add('dateStart')
                ->add('dateEnd')
                ->add('Location')
                ->add('establishment')
                ->add('basePrice')
                ->add('description')
                ->add('animators')
                ->add('state')
            ->end();
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Details', array('class' => 'col-md-4'))
                ->add('name')
                ->add('location', 'entity', array(
                    'class' => 'AppBundle\Entity\Location',
                    'property' => 'name'
                ))
                ->add('dateStart', 'datetime', array(
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text'
                ))
                ->add('dateEnd', 'datetime', array(
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text'
                ))
                ->add('basePrice')
            ->end()
            ->with('Details 2', array('class' => 'col-md-4'))
                ->add('state', 'choice', array(
                    'choices' => Event::getStates()
                ))
                ->add('animators', 'entity', array(
                    'class' => 'AppBundle\Entity\User',
                    'property' => 'fullName',
                    'multiple' => true
                ))
                ->add('imageFile', 'vich_image', array(
                    'required' => false,
                    'allow_delete' => true,
                    'download_link' => false,
                ))
            ->end()
            ->with('Description')
                ->add('description', null, array(
                    'attr' => array('rows' => '10')
                ))
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
            ->add('animators', null, array(), 'entity', array(
                'property' => 'fullName'
            ))
            ->add('state', null, array(), 'choice', array(
                'choices' => Event::getStates(),
            ))
            ->add('establishment');
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function toString($object)
    {
        return $object instanceof Event
            ? $object->getName()
            : 'Nouvel évènement'; // shown in the breadcrumb on the create view
    }
}