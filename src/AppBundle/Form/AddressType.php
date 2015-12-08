<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24.10.2015
 * Time: 15:05
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddressType
 *
 * @package AppBundle\Form
 */
class AddressType extends AbstractType
{
    /**
     * @inheritdoc
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('street')
            ->add('streetNumber')
            ->add('zipCode')
            ->add('city')
            ->add('province')
            ->add('country', 'country', array(
                'placeholder' => 'Choose a country',
                'preferred_choices' => array('CH')
            ));
    }

    /**
     * @inheritdoc
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Address',
            'cascade_validation' => true,
            'error_bubbling' => true
        ));
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getName()
    {
        return 'address';
    }
}