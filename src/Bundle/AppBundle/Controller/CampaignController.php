<?php

namespace Bundle\AppBundle\Controller;

use Bundle\AppBundle\Entity\Campaign;
use Bundle\AppBundle\Entity\CampaignFile;
use Bundle\AppBundle\Entity\Category;
use Bundle\AppBundle\Form\CampaignSearchType;
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
        
        $campaignList = $this->getDoctrine()->getRepository('BundleAppBundle:Campaign')
                             ->findBy(array('createdBy'=>$this->getUser(),'organization'=>null));
        $campaignFiles = $this->getDoctrine()
                              ->getRepository('BundleAppBundle:CampaignFile')
                              ->findBy(array('createdBy'=>$this->getUser()));
    

        return $this->render('BundleAppBundle:Campaign:home.html.twig',array(
            'campaigns'=>$campaignList,
            'user' =>$this->getUser()->getProfile(),
            'campaignFiles' =>$campaignFiles
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

        if($request->request->get('organizationVal')){
            $organizationId = $request->request->get('organizationVal');
            $organizationName = $this->getDoctrine()->getRepository('BundleAppBundle:Organization')->find($organizationId);
        } else {
            $organizationName = null ;
        }

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
    public function individualCampaignUpdateAction(Request $request , Campaign $campaign)
    {

        if($this->isFacebookLogin()){
            return $this->isFacebookLogin();
        }
        $organization = $this->checkExistingOrganization($this->getUser());
        
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
            'BundleAppBundle:Campaign:individualCampaignEditform.html.twig',
            array(
                'form'     => $form->createView(),
                'user'      => $user,
                'organization' => $organization,
                'campaign' =>$campaign
            )
        );

    }
    
    public  function campaignDetailsAction(Campaign $campaign){

        $campaignGallary = $this->getDoctrine()
            ->getRepository('BundleAppBundle:CampaignFile')
            ->findBy(array('campaign'=>$campaign));

        return $this->render(
            'BundleAppBundle:Campaign:campaignDetail.html.twig',
            array(
                'campaign'      => $campaign,
                'campaignGallary' => $campaignGallary
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
        $campaign->setStatus(false);
        $campaignRepo = $this->getDoctrine()->getRepository("BundleAppBundle:Campaign");
        $campaignRepo->create($campaign);
    }

    public function categoryBasedCampaignListAction(Request $request ,Category $category)
    {
        list($form, $data) = $this->campaignSearch($request);

        $campaignList = $this->paginate($this->getDoctrine()
                             ->getRepository('BundleAppBundle:Campaign')
                             ->findByCategory(array('category'=>$category)));
        
        $categoryList = $this->getDoctrine()
                             ->getRepository('BundleAppBundle:Category')
                             ->findAll();

        return $this->render('BundleAppBundle:Dashboard:home.html.twig',array(
            'campaigns'=>$campaignList,
            'categories'=>$categoryList,
            'categoryTitle'=>$category,
            'form' => $form->createView()

        ));
    }
     public function campaignSearchAction(Request $request)
    {

        list($form, $data) = $this->campaignSearch($request);

        $searchResult = $this->paginate($this->getDoctrine()
                             ->getRepository('BundleAppBundle:Campaign')
                             ->getSearchResult($data));

        $categoryList = $this->getDoctrine()
                             ->getRepository('BundleAppBundle:Category')
                             ->findAll();

        return $this->render('BundleAppBundle:Dashboard:home.html.twig',array(
            'campaigns'=>$searchResult,
            'categories'=>$categoryList,
            'categoryTitle'=>'',
            'form' => $form->createView()

        ));
    }
    public function campaignSearchCategoryWiseAction(Request $request,Category $category)
    {        

        list($form, $data) = $this->campaignSearch($request);

        $searchResult = $this->paginate($this->getDoctrine()
                             ->getRepository('BundleAppBundle:Campaign')
                             ->getSearchResult($data,$category));

        $categoryList = $this->getDoctrine()
                             ->getRepository('BundleAppBundle:Category')
                             ->findAll();

        return $this->render('BundleAppBundle:Dashboard:home.html.twig',array(
            'campaigns'=>$searchResult,
            'categories'=>$categoryList,
            'categoryTitle'=>$category,
            'form' => $form->createView()

        ));
    }

    /**
     * @param Request $request
     * @return array
     */
    private function campaignSearch(Request $request)
    {
        $form = new CampaignSearchType();
        $data = $request->get($form->getName());
        $form = $this->createForm($form);
        $form->submit($data);

        return array($form, $data);
    }


}
