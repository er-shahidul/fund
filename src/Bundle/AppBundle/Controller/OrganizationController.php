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
                
                $this->saveOrganization($organization);

                $massage = 'Organization Successfully Inserted';
                $this->get('session')->getFlashBag()->add('notice', $massage);
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
                
                $this->saveOrganization($organization);

                $massage = 'Organization Successfully Inserted';
                $this->get('session')->getFlashBag()->add('notice', $massage);
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
        $organization->setStatus('Active');
        $organizationRepo = $this->getDoctrine()->getRepository("BundleAppBundle:Organization");
        $organizationRepo->create($organization);
    }

    
}
