<?php

namespace Bundle\AppBundle\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrganizationType extends AbstractType
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
            ->add('address', 'textarea', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('mobileNumber', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('email', 'text', array(
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
            'data_class' => 'Bundle\AppBundle\Entity\Organization'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_organization';
    }
}
