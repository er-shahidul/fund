<?php

namespace Bundle\AppBundle\Form;

use Bundle\AppBundle\Entity\Organization;
use Bundle\AppBundle\Repository\OrganizationRepository;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CampaignSearchType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', 'text', array(
                'required' => false,
                'mapped'=>false,
                'attr' => array('class' => 'col-md-3 col-sm-3 form-control input-large')
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bundle\AppBundle\Entity\Campaign'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_search';
    }
}
