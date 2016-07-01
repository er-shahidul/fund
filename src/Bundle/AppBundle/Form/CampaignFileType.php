<?php

namespace Bundle\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CampaignFileType extends AbstractType
{

    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file',array(
                'multiple'=>true
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            'data_class'=>'Bundle\AppBundle\Entity\CampaignFile',
            'csrf_protection'=>true,
            'csrf_field_name'=>'_token',
            'intention'=>'file'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'campaignfile';
    }
}
