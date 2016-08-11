<?php

namespace Bundle\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class DonationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('last_name', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('email', 'email', array(
                'required' => true,
                'invalid_message' => 'Email is not valid',
                'attr' => array('class' => 'form-control input-large'),
                
            )) 
            ->add('postalCode', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large'),

            ))
            ->add('donate_amount', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('notes', 'textarea', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('anonymous', 'checkbox', array(
                'required' => false,
                'attr' => array('class' => 'icheck')
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bundle\AppBundle\Entity\Donation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_donation';
    }
}
