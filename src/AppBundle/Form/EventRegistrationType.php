<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 24.10.2015
 * Time: 15:05
 */

namespace AppBundle\Form;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Form\Transformer\EntityToIdObjectTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EventRegistrationType
 *
 * @package AppBundle\Form
 */
class EventRegistrationType extends AbstractType
{
    /**
     * @inheritdoc
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'mapped' => false
            ))
            ->add('eventId', 'text', array(
                'mapped' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Registration',
            'csrf_protection' => false
        ));
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getName()
    {
        return 'rest_event_registration';
    }
}