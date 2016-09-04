<?php
namespace Bundle\AppBundle\Form\Search;

use Bundle\AppBundle\Repository\OrganizationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CampaignSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'entity',
                array(
                    'property' => 'title',
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'required'=>false,
                    'class' => 'Bundle\AppBundle\Entity\Category',
                    'placeholder' => 'Select Category',
                )
            )
            ->add('organization', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'required'=>false,
                    'read_only'=>true,
                    'class' => 'Bundle\AppBundle\Entity\Organization',
                    'placeholder' => 'Select Organization',
                    'query_builder' => function (OrganizationRepository $repository)
                    {
                        return $repository->createQueryBuilder('o')
                            ->where('o.status = :status')
                            ->setParameter('status', 'Active');
                    }
                )
            )
            ->add('start_date', 'text',array(
                'attr' => array(
                    'placeholder' => 'Start Date'
                ),
                'read_only' => true
            ))
            ->add('end_date', 'text',array(
                'attr' => array(
                    'placeholder' => 'End Date'
                ),
                'read_only' => true
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'campaign_search';
    }
}