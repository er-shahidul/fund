<?php

namespace Bundle\AppBundle\Controller;

use Bundle\AppBundle\Entity\Campaign;
use Bundle\AppBundle\Entity\Organization;
use Bundle\AppBundle\Form\CampaignType;
use Bundle\AppBundle\Form\OrganizationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrganizationController extends Controller
{
    public function indexAction()
    {
        return $this->render('BundleAppBundle:Organization:home.html.twig');
    } 
    public function createAction(Request $request)
    {

        $organization = new Organization();

        $form = $this->createForm(new OrganizationType(), $organization);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                if($this->checkDuplicateOrganization($organization)){

                    $massage = 'Organization already Exist';
                    $this->get('session')->getFlashBag()->add('error', $massage);
                    return $this->redirect($this->generateUrl('organization_create'));
                } else {
                    $this->saveOrganization($organization);
                    $massage = 'Organization Successfully Inserted';
                    $this->get('session')->getFlashBag()->add('success', $massage);
                }


                return $this->redirect($this->generateUrl('organization_list'));
            }
        }

        return $this->render(
            'BundleAppBundle:Organization:form.html.twig',
            array(
                'form'     => $form->createView()
            )
        );
        
    } 
    
    public function organizationCreateAjaxAction(Request $request)
    {

        $organization = new Organization();

        $form = $this->createForm(new OrganizationType(), $organization);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                if($this->checkDuplicateOrganization($organization)){

                    $massage = 'Organization already Exist';
                    $this->get('session')->getFlashBag()->add('error', $massage);
                    return $this->redirect($this->generateUrl('organization_create'));
                } else {

                    $this->saveOrganization($organization);
                    $massage = 'Organization Successfully Inserted';
                    $this->get('session')->getFlashBag()->add('success', $massage);
                }

                $massage = 'Organization Successfully Inserted';
                $this->get('session')->getFlashBag()->add('success', $massage);
                return $this->redirect($this->generateUrl('campaign_create'));
            }
        }

        return $this->render(
            'BundleAppBundle:Organization:formAjax.html.twig',
            array(
                'form'     => $form->createView()
            )
        );
        
    }
    
    public  function saveOrganization(Organization $organization) {

        $organization->setCreatedDate(new \DateTime(date('Y-m-d')));
        $organization->setCreatedBy($this->getUser());
        $organization->setValidateOrganization(strtolower(str_replace(' ','',$organization->getName())));
        $organization->setStatus('Active');
        $organizationRepo = $this->getDoctrine()->getRepository("BundleAppBundle:Organization");
        $organizationRepo->create($organization);
    }

    public function onLoadOrganizationCreateAction(Request $request) {

        $organization = new Organization();
        $organizationList = $this->getDoctrine()
                                 ->getRepository("BundleAppBundle:Organization")
                                 ->findBy(array('createdBy'=>$this->getUser()->getId()));


        $form = $this->createForm(new OrganizationType(), $organization);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                if($this->checkDuplicateOrganization($organization)){

                    $massage = 'Organization already Exist';
                    $this->get('session')->getFlashBag()->add('error', $massage);
                    return $this->redirect($this->generateUrl('organization_create'));
                } else {

                    $this->saveOrganization($organization);
                    $massage = 'Organization Successfully Inserted';
                    $this->get('session')->getFlashBag()->add('success', $massage);
                }

                return $this->redirect($this->generateUrl('organization_list'));
            }
        }

        return $this->render(
            'BundleAppBundle:Organization:loadOrganization.html.twig',array(
            'form'=>$form->createView(),
            'organizationList' => $organizationList
        ));
    }
    public function checkDuplicateOrganization(Organization $organization) {

        $name = strtolower(str_replace(' ','',$organization->getName()));
        $org =  $this->getDoctrine()
              ->getRepository('BundleAppBundle:Organization')
              ->findBy(array('validateOrganization'=>$name));
        return $org;
    }
    
}
