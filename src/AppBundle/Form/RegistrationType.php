<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24.10.2015
 * Time: 01:20
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lastName')
            ->add('firstName')
            ->add('gender', 'choice', array(
                'placeholder' => 'Choisissez une option',
                'choices' => array(
                    'male' => 'Male',
                    'female' => 'Female'
                )
            ))
            ->add('birthDate', 'birthday')
            ->add('locationsOfInterest', 'entity', array(
                'multiple' => true,
                'class' => 'AppBundle\Entity\Location',
                'required' => false
            ));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'app_user_registration';
    }
}