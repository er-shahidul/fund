<?php

namespace Bundle\AppBundle\Controller;

use Bundle\AppBundle\Entity\Campaign;
use Bundle\AppBundle\Entity\CampaignFile;
use Bundle\AppBundle\Form\CampaignType;
use Bundle\UserBundle\Entity\User;
use Bundle\UserBundle\Form\UserType;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class CampaignController extends BaseController
{
    
    public function indexAction()
    {
         if($this->isFacebookLogin()){
             return $this->isFacebookLogin();
         }
        
        $campaignList= $this->getDoctrine()->getRepository('BundleAppBundle:Campaign')
                             ->findBy(array('createdBy'=>$this->getUser()));
        
        return $this->render('BundleAppBundle:Campaign:home.html.twig',array(
            'campaigns'=>$campaignList,
            'user' =>$this->getUser()->getProfile()
        ));
    }
    public function individualCampaignListAction()
    {
         if($this->isFacebookLogin()){
             return $this->isFacebookLogin();
         }
        
        $campaignList= $this->getDoctrine()->getRepository('BundleAppBundle:Campaign')
                             ->findBy(array('createdBy'=>$this->getUser(),'organization'=>null));

        return $this->render('BundleAppBundle:Campaign:home.html.twig',array(
            'campaigns'=>$campaignList,
            'user' =>$this->getUser()->getProfile()
        ));
    }
    public function organizationalCampaignListAction()
    {
         if($this->isFacebookLogin()){
             return $this->isFacebookLogin();
         }
        $organization = $this->getDoctrine()->getRepository('BundleAppBundle:Organization')
                                            ->findBy(array('createdBy'=>$this->getUser()));
        
        $campaignList= $this->getDoctrine()->getRepository('BundleAppBundle:Campaign')
                             ->findBy(array('createdBy'=>$this->getUser(),'organization'=>$organization));

        return $this->render('BundleAppBundle:Campaign:home.html.twig',array(
            'campaigns'=>$campaignList,
            'user' =>$this->getUser()->getProfile()
        ));
    }
    public function createAction(Request $request)
    {

        if(!$this->getUser()){

           return  $this->redirect($this->generateUrl('hwi_oauth_service_redirect',array('service'=>'facebook')));
        }
        $organization = $this->checkExistingOrganization($this->getUser());

        $campaign = new Campaign();

        $form = $this->createForm(new CampaignType($this->getUser(),null), $campaign);



        if ('POST' == $request->getMethod()) {


            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->saveCampaign($campaign);

                $massage = 'Campaign Successfully Inserted';
                $this->get('session')->getFlashBag()->add('notice', $massage);
                return $this->redirect($this->generateUrl('campaign_list'));
            }
        }
        $user = $this->getUser()->getProfile();
     
        return $this->render(
            'BundleAppBundle:Campaign:form.html.twig',
            array(
                'form'     => $form->createView(),
                'user'      => $user,
                'organization' => $organization
            )
        );
        
    }

    public function organizationCampaignCreateAction(Request $request)
    {
        if($this->isFacebookLogin()){
            return $this->isFacebookLogin();
        }
        
        $organizationId = $request->request->get('organizationVal');
        $organizationName = $this->getDoctrine()->getRepository('BundleAppBundle:Organization')->find($organizationId);
        
        $organization = $this->checkExistingOrganization($this->getUser());

        $campaign = new Campaign();

        $form = $this->createForm(new CampaignType($this->getUser(),$organizationName), $campaign);

        if ('POST' == $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $files = $request->files->get('appbundle_campaign[campaignFiles]', null, true);

                $this->getDoctrine()
                    ->getRepository('BundleAppBundle:CampaignFile')
                    ->saveCampaignFile($files,$campaign,$this->getUser());
                
                $this->saveCampaign($campaign);

                $massage = 'Campaign Successfully Inserted';
                $this->get('session')->getFlashBag()->add('notice', $massage);
                return $this->redirect($this->generateUrl('campaign_list'));
            }
        }
        $user = $this->getUser()->getProfile();

        return $this->render(
            'BundleAppBundle:Campaign:organizationCampaignForm.html.twig',
            array(
                'form'     => $form->createView(),
                'user'      => $user,
                'organization' => $organization
            )
        );

    }
    public function individualCampaignCreateAction(Request $request)
    {
        
        if($this->isFacebookLogin()){
            return $this->isFacebookLogin();
        }
        $organization = $this->checkExistingOrganization($this->getUser());

        $campaign = new Campaign();

        $form = $this->createForm(new CampaignType($this->getUser(),null), $campaign);

        if ('POST' == $request->getMethod()) {
            
            $form->handleRequest($request);

            if ($form->isValid()) {

                $files = $request->files->get('appbundle_campaign[campaignFiles]', null, true);
        
                $this->getDoctrine()
                     ->getRepository('BundleAppBundle:CampaignFile')
                     ->saveCampaignFile($files,$campaign,$this->getUser());
                
                $this->saveCampaign($campaign);

                $massage = 'Campaign Successfully Inserted';
                $this->get('session')->getFlashBag()->add('notice', $massage);
                return $this->redirect($this->generateUrl('campaign_list'));
            }
        }
        $user = $this->getUser()->getProfile();

        return $this->render(
            'BundleAppBundle:Campaign:individualCampaignform.html.twig',
            array(
                'form'     => $form->createView(),
                'user'      => $user,
                'organization' => $organization
            )
        );

    }
    
    public  function campaignDetailsAction(Campaign $campaign){
        
        return $this->render(
            'BundleAppBundle:Campaign:campaignDetail.html.twig',
            array(
                'campaign'      => $campaign,
            )
        );
    }
    
    private function checkExistingOrganization(User $user) {

        return  $this->getDoctrine()
                             ->getRepository("BundleAppBundle:Organization")
                             ->findOneBy(array('createdBy'=>$user->getId()));

    }
    
    public  function saveCampaign(Campaign $campaign) {

        $campaign->setCreatedDate(new \DateTime(date('Y-m-d')));
        $campaign->setCreatedBy($this->getUser());
        $campaignRepo = $this->getDoctrine()->getRepository("BundleAppBundle:Campaign");
        $campaignRepo->create($campaign);
    }

    
}
