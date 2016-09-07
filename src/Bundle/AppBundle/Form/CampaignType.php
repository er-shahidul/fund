<?php

namespace Bundle\AppBundle\Form;

use Bundle\AppBundle\Entity\Organization;
use Bundle\AppBundle\Repository\OrganizationRepository;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CampaignType extends AbstractType
{

    private $user;
    private $organization = null;

    public function __construct($user, $organization)
    {

        $this->user = $user;
        $this->organization = $organization;

    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('campaignVideoUrl', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large')
            ));
            if($this->user->hasRole("ROLE_ADMIN")) {
                $builder->add(
                    'status',
                    'choice',
                    array(
                        'choices'  => array(
                            true  => 'Published',
                            false => 'Un-published'
                        ),
                        'multiple' => false,
                        'expanded' => true,
                        'required' => true,
                        'data'     => false
                    )
                );
            }
        $builder->add('amount', 'text', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large')
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'attr' => array('class' => 'form-control input-large'),
            ))
            ->add('category', 'entity',
                array(
                    'property' => 'title',
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'required'=>false,
                    'class' => 'Bundle\AppBundle\Entity\Category',
                    'placeholder' => 'Select Category',
                )
            )
            ->add('location', 'entity',
                array(
                    'property' => 'name',
                    'attr' => array('class' => 'form-control select2 input-medium'),
                    'required'=>false,
                    'class' => 'Bundle\AppBundle\Entity\Location',
                    'placeholder' => 'Select Location',
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
                            ->andWhere('o.createdBy =:user')
                            ->setParameter('user',$this->user)
                            ->setParameter('status', 'Active');
                    },

                    'data'=> $this->organization ? $this->organization:null
                )
            )
            ->add($builder->create('endOfCampaignDate', 'text', array(
                'attr' => array(
                    'placeholder' => 'Date'
                ),
                'required'=>false,
                'data_class' => null
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d')))
            
            ->add('campaignFiles','file', array(
                'multiple'=>true,
                'required' => false,
                'mapped'=>false,
                'constraints' => new NotBlank()
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
        return 'appbundle_campaign';
    }
}
