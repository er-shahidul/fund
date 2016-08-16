<?php

namespace Bundle\AppBundle\Controller;

use Bundle\AppBundle\Entity\Campaign;
use Bundle\AppBundle\Entity\Organization;
use Bundle\AppBundle\Form\CampaignType;
use Bundle\AppBundle\Form\OrganizationType;
use Bundle\UserBundle\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationController extends BaseController
{
    public function indexAction()
    {
        if($this->isFacebookLogin()) {

            return $this->isFacebookLogin();
        }

        if($this->getUser()->hasRole('ROLE_ADMIN')) {
        $organizationList = $this->paginate($this->getDoctrine()
                                 ->getRepository('BundleAppBundle:Organization')
                                 ->findAll());
        }else {
            $organizationList = $this->paginate($this->getDoctrine()
                ->getRepository('BundleAppBundle:Organization')
                ->findBy(array('createdBy'=>$this->getUser(),'status'=>'Active')));
        }
        return $this->render('BundleAppBundle:Organization:home.html.twig',array(
            'organizationsList' =>$organizationList
        ));
    } 
    public function createAction(Request $request)
    {
        if($this->isFacebookLogin()){
            return $this->isFacebookLogin();
        }

        $organization = new Organization();

        $form = $this->createForm(new OrganizationType(), $organization);

        if ('POST' == $request->getMethod()) {

            $form->handleRequest($request);
      
            if ($form->isValid()) {
                /** @var UploadedFile $file */
                $organization->upload();
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
               // return $this->redirect($this->generateUrl('campaign_create'));
                return $this->redirect($this->generateUrl('campaign_organization_create',array('organizationVal'=>$organization->getId())));
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
        if($this->userEmailAndPhoneVerifyCheck()){
            return $this->render(
                'BundleAppBundle:Organization:loadOrganization.html.twig',array(
                'form'=>$form->createView(),
                'organizationList' => $organizationList
            ));    
        } else {
            
            return $this->redirect($this->generateUrl('campaign_user-profile-verify'));

        }
        
    }
    public function loadOrganizationListAction(Request $request) {


        $organization = new Organization();
        $organizationList = $this->getDoctrine()
                                 ->getRepository("BundleAppBundle:Organization")
                                 ->findBy(array('createdBy'=>$this->getUser()->getId()));
        return $this->render(
            'BundleAppBundle:Organization:loadOrganizationList.html.twig',array(
            'organizationList' => $organizationList
        ));
        
    }
    public function isFacebookLogin()
    {
        if(!$this->getUser()){
            return  $this->redirect($this->generateUrl('hwi_oauth_service_redirect',array('service'=>'facebook')));
        }
    }
    public function userEmailAndPhoneVerifyCheck(){
        
        $userPhone = $this->getUser()->getProfile()->getConfirmationTokenPhoneVerify();
        $userEmail = $this->getUser()->getProfile()->getConfirmationTokenEmailVerify();
        if($userPhone && $userEmail){
            return true;
        }

    }
    public function checkDuplicateOrganization(Organization $organization) {

        $name = strtolower(str_replace(' ','',$organization->getName()));
        $org =  $this->getDoctrine()
              ->getRepository('BundleAppBundle:Organization')
              ->findBy(array('validateOrganization'=>$name));
        return $org;
    }

    public function changeStatusAction(Organization $organization){


        if($organization->getStatus() === 'Active'){
            $organization->setStatus('Inactive');

            $massage = 'Organization Successfully Not Approved';
            $this->get('session')->getFlashBag()->add('success', $massage);
        } else{
            $organization->setStatus('Active');

            $massage = 'Organization Successfully Approved';
            $this->get('session')->getFlashBag()->add('success', $massage);
        }
         $this->getDoctrine()->getRepository('BundleAppBundle:Organization')->persist($organization);

        return $this->redirect($this->generateUrl('organization_list'));
    }
    public function organizationDetailAction(Organization $organization){

        $campaignList= $this->getDoctrine()->getRepository('BundleAppBundle:Campaign')
            ->findBy(array('organization'=>$organization));
        
        return $this->render(
            'BundleAppBundle:Organization:detailOrganization.html.twig',array(
            'campaigns'=>$campaignList,
            'organization' => $organization
        ));

    }
    
}
