<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChangePasswordFormType extends AbstractType
{

    private $newEntry;

    /**
     * @param bool   $newEntry
     */
    public function __construct($newEntry = true)
    {
        $this->newEntry = $newEntry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($this->newEntry){
            $builder
                ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation')
                ));
        }
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bundle\UserBundle\Entity\User',
            'validation_groups'  => 'registration_password',
        ));
    }

    public function getName()
    {
        return 'manage_user_password';
    }
}
