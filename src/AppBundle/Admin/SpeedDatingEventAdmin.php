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

class SpeedDatingEventAdmin extends EventAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Age and capacity', array('class' => 'col-md-4'))
                ->add('maxPeople')
                ->add('menSeats')
                ->add('womenSeats')
                ->add('minAge')
                ->add('maxAge')
                ->add('establishment')
            ->end();

        parent::configureFormFields($formMapper);
    }

    /**
     * @inheritdoc
     */
    public function toString($object)
    {
        return $object instanceof Event
            ? $object->getName()
            : 'New Speed Dating event'; // shown in the breadcrumb on the create view
    }
}