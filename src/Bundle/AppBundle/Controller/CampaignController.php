<?php

namespace Bundle\AppBundle\Controller;

use Bundle\AppBundle\Entity\Campaign;
use Bundle\AppBundle\Entity\CampaignDetails;
use Bundle\AppBundle\Entity\CampaignFile;
use Bundle\AppBundle\Entity\Category;
use Bundle\AppBundle\Form\CampaignDetailType;
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
        
        $campaignList= $this->paginate($this->getDoctrine()->getRepository('BundleAppBundle:Campaign')
                             ->findBy(array('createdBy'=>$this->getUser())));
        
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
        
        $campaignList = $this->paginate($this->getDoctrine()->getRepository('BundleAppBundle:Campaign')
                             ->findBy(array('createdBy'=>$this->getUser(),'organization'=>null)));
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
        
        $campaignList= $this->paginate($this->getDoctrine()->getRepository('BundleAppBundle:Campaign')
                             ->findBy(array('createdBy'=>$this->getUser(),'organization'=>$organization)));

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
        if($request->query->get('organizationVal')){
            $organizationId = $request->query->get('organizationVal');
            $organizationName = $this->getDoctrine()->getRepository('BundleAppBundle:Organization')->find($organizationId);
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
//var_dump($campaign->getOrganization());die;
        if($this->isFacebookLogin()){
            return $this->isFacebookLogin();
        }
        $organization = $this->checkExistingOrganization($this->getUser());
        
        $form = $this->createForm(new CampaignType($this->getUser(),$campaign->getOrganization()), $campaign);

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
                'organization' => $campaign->getOrganization(),
                'campaign' =>$campaign
            )
        );

    }
    
    public  function campaignDetailsAction(Request $request , Campaign $campaign){

        $campaignGallary = $this->getDoctrine()
            ->getRepository('BundleAppBundle:CampaignFile')
            ->findBy(array('campaign'=>$campaign));
        
        $campaignDetailList = $this->getDoctrine()
            ->getRepository('BundleAppBundle:CampaignDetails')
            ->findBy(array('campaign'=>$campaign),array('id'=>'DESC'));

        $recentDonations = $this->getDoctrine()
            ->getRepository('BundleAppBundle:Donation')
            ->findBy(array('campaign'=>$campaign),array('id'=>'DESC'));

        $campaignDetail = new CampaignDetails();

        $form = $this->createForm(new CampaignDetailType(), $campaignDetail);

        if ('POST' == $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {


                $file = $request->files->get('appbundle_campaign_details[file]', null, true);
                if(!empty($file)){
                    
                $campaignDetail->setFile($file);
                $campaignDetail->upload();
                $campaignDetail->setFileType($file->getClientMimeType());
                $campaignDetail->setFileName($file->getClientOriginalName());

                }

                /*$this->getDoctrine()
                    ->getRepository('BundleAppBundle:CampaignFile')
                    ->saveCampaignFile($files,$campaign,$this->getUser());*/
                
                $this->saveCampaignDetail($campaignDetail,$campaign);

                return $this->redirect($this->generateUrl('campaign_detail',array('id'=>$campaign->getId())));
            }
        }

        return $this->render(
            'BundleAppBundle:Campaign:campaignDetail.html.twig',
            array(
                'form'            => $form->createView(),
                'campaign'        => $campaign,
                'campaignGallary' => $campaignGallary,
                'campaignDetailList'  => $campaignDetailList,
                'recentDonations' =>$recentDonations
            )
        );
    }
    public  function campaignDetailsUpdateAction(Request $request , CampaignDetails $campaignDetail){

        $campaignGallary = $this->getDoctrine()
            ->getRepository('BundleAppBundle:CampaignFile')
            ->findBy(array('campaign'=>$campaignDetail->getCampaign()));
        
        $campaignDetailList = $this->getDoctrine()
            ->getRepository('BundleAppBundle:CampaignDetails')
            ->findBy(array('campaign'=>$campaignDetail->getCampaign()),array('id'=>'DESC'));
        
        $form = $this->createForm(new CampaignDetailType(), $campaignDetail);

        if ('POST' == $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {


                /*$files = $request->files->get('appbundle_campaign[campaignFiles]', null, true);


                $this->getDoctrine()
                    ->getRepository('BundleAppBundle:CampaignFile')
                    ->saveCampaignFile($files,$campaign,$this->getUser());*/
                
                $this->saveCampaignDetail($campaignDetail,$campaignDetail->getCampaign());

                return $this->redirect($this->generateUrl('campaign_detail',array('id'=>$campaignDetail->getCampaign()->getId())));
            }
        }

        return $this->render(
            'BundleAppBundle:Campaign:campaignDetailUpdate.html.twig',
            array(
                'form'            => $form->createView(),
                'campaign'        => $campaignDetail->getCampaign(),
                'campaignDetail' => $campaignDetail,
                'campaignGallary' => $campaignGallary,
                'campaignDetailList'  => $campaignDetailList
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
    public  function saveCampaignDetail(CampaignDetails $campaignDetail,$campaign) {

        $campaignDetail->setCreatedDate(new \DateTime(date('Y-m-d')));
        $campaignDetail->setCreatedBy($this->getUser());
        $campaignDetail->setCampaign($campaign);
        $campaignDetailRepo = $this->getDoctrine()->getRepository("BundleAppBundle:CampaignDetails");
        $campaignDetailRepo->create($campaignDetail);
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

        return $this->render('BundleAppBundle:Dashboard:categoryBasedHome.html.twig',array(
            'campaigns'=>$campaignList,
            'categories'=>$categoryList,
            'categoryTitle'=>$category,
            'form' => $form->createView()

        ));
    }
    public function featuresCampaignListAction()
    {
        $campaignList = $this->paginate($this->getDoctrine()
                             ->getRepository('BundleAppBundle:Campaign')
                             ->findBy(array('feature'=>1)));
        $categoryList = $this->getDoctrine()
            ->getRepository('BundleAppBundle:Category')
            ->findAll();

        return $this->render('BundleAppBundle:Dashboard:featureBasedHome.html.twig',array(
            'campaigns'=>$campaignList,
            'categories'=>$categoryList,
            'status' => 'feature'

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

    public function getCampaignSingleImageAction(Campaign $campaign){

        $campaignGallary = $this->getDoctrine()
            ->getRepository('BundleAppBundle:CampaignFile')
            ->findBy(array('campaign'=>$campaign));

        if(!empty($campaignGallary)){
            $imagePath = $campaignGallary[0]->getPath();

            $path = '/uploads/campaign/'.$imagePath;
            return new Response($path);
        } else {

            $path = '/assets/img/people.png';
            return new Response($path);
        }
        
    }
    public function getCampaignProgressBarAction(Campaign $campaign){


        $recentDonations = $this->getDoctrine()
            ->getRepository('BundleAppBundle:Donation')
            ->findBy(array('campaign'=>$campaign),array('id'=>'DESC'));

        if(!empty($recentDonations)) {

            $totalDonationAmount = 0;
            foreach ($recentDonations as $recentDonation){
                $totalDonationAmount += $recentDonation->getDonateAmount();
            }
            $percentage = ($totalDonationAmount * 100) / $campaign->getAmount();
            $roundPercentage = round($percentage);
            return new Response("<span class=\"funded\">$roundPercentage%</span>");
        } else{
            return new Response("<span class=\"funded\">0%</span>");
        }


    }
    public function getCampaignProgressBarPercentageAction(Campaign $campaign){


        $recentDonations = $this->getDoctrine()
            ->getRepository('BundleAppBundle:Donation')
            ->findBy(array('campaign'=>$campaign),array('id'=>'DESC'));

        if(!empty($recentDonations)) {

            $totalDonationAmount = 0;
            foreach ($recentDonations as $recentDonation){
                $totalDonationAmount += $recentDonation->getDonateAmount();
            }
            $percentage = ($totalDonationAmount * 100) / $campaign->getAmount();
            $roundPercentage = round($percentage);
            return new Response("$roundPercentage%");
        } else{
            return new Response("0%");
        }


    }
    public function getCampaignTargetAmountAction(Campaign $campaign){


        $recentDonations = $this->getDoctrine()
            ->getRepository('BundleAppBundle:Donation')
            ->findBy(array('campaign'=>$campaign),array('id'=>'DESC'));
        

            $totalDonationAmount = 0;
            foreach ($recentDonations as $recentDonation){
                $totalDonationAmount += $recentDonation->getDonateAmount();
            }
            return new Response("$totalDonationAmount");
      
        
    }
    public function getSuccessFullCampaignAction(Campaign $campaign){


        $recentDonations = $this->getDoctrine()
            ->getRepository('BundleAppBundle:Donation')
            ->findBy(array('campaign'=>$campaign),array('id'=>'DESC'));

        if(!empty($recentDonations)) {

            $totalDonationAmount = 0;
            foreach ($recentDonations as $recentDonation){
                $totalDonationAmount += $recentDonation->getDonateAmount();
            }
            $percentage = ($totalDonationAmount * 100) / $campaign->getAmount();
            $roundPercentage = round($percentage,2);
            if($roundPercentage >= 100){
                return new Response("<span class=\"campaign-successful\">SUCCESSFUL</span>");
            } else{
                return new Response("<span class=\"campaign-unsuccessful\">UNSUCCESSFUL</span>");
            }
        } else{
            return new Response("<span class='campaign-unsuccessful'>UNSUCCESSFUL</span>");
        }


    }
    public function getCampaignBackersAction(Campaign $campaign){
        
        $recentDonations = $this->getDoctrine()
            ->getRepository('BundleAppBundle:Donation')
            ->findBy(array('campaign'=>$campaign),array('id'=>'DESC'));

        if(!empty($recentDonations)) {

            $totalBackers = count($recentDonations);
            return new Response("$totalBackers");
        } else{
            return new Response("0");
        }


    }
    public function getCountCampaignByCategoriesWiseAction(Category $category){
        
        $countCampaignByCategories = $this->getDoctrine()
            ->getRepository('BundleAppBundle:Campaign')
            ->countCampaignByCategories($category);

        if(!empty($countCampaignByCategories)) {

            return new Response("$countCampaignByCategories");
        } else{
            return new Response("0");
        }


    }
    public function getCountFeatureAction(){
        
        $countFeatureCampaign = $this->getDoctrine()
            ->getRepository('BundleAppBundle:Campaign')
            ->countFeatureCampaign();

        if(!empty($countFeatureCampaign)) {

            return new Response("$countFeatureCampaign");
        } else{
            return new Response("0");
        }


    }
    public function campaignFeaturedAction(Request $request){

        $campaign = $this->getDoctrine()
            ->getRepository('BundleAppBundle:Campaign')
            ->find($request->request->all()['campaign']);
        if($campaign->isFeature() == false) {

            $campaign->setFeature(true);
            $msg = 'Featured';
        } else {
            $campaign->setFeature(false);
            $msg = 'Un-featured';
        }
        $campaignRepo = $this->getDoctrine()->getRepository("BundleAppBundle:Campaign");
        $campaignRepo->flashData($campaign);
        return new Response($msg);

    }


}
