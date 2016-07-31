<?php

namespace Bundle\AppBundle\Form;

use Bundle\AppBundle\Entity\Organization;
use Bundle\AppBundle\Repository\OrganizationRepository;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CampaignDetailType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('notes', 'textarea', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('campaignDetailVideoUrl', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('file','file', array(
                'required' => false,
                'mapped'=>false
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bundle\AppBundle\Entity\CampaignDetails'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_campaign_details';
    }
}
