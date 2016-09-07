<?php

namespace Bundle\AppBundle\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('geocode', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large')
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bundle\AppBundle\Entity\Location'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_location';
    }
}
