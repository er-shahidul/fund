<?php

namespace Bundle\AppBundle\Controller;

use Bundle\AppBundle\Form\CampaignSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends BaseController
{
    public function indexAction(Request $request)
    {
        
        $campaignList = $this->paginate($this->getDoctrine()->getRepository('BundleAppBundle:Campaign')->findAll());
        $categoryList = $this->getDoctrine()->getRepository('BundleAppBundle:Category')->findAll();
      
        $form = new CampaignSearchType();
        $form = $this->createForm($form);
        
        return $this->render('BundleAppBundle:Dashboard:home.html.twig',array(
            'campaigns'=>$campaignList,
            'categories'=>$categoryList,
            'categoryTitle'=>'',
            'form' => $form->createView()

        ));
    }


}
