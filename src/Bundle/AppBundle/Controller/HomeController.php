<?php

namespace Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends BaseController
{
    public function indexAction()
    {
     //   var_dump($this->getUser()->getRoles());die;
        $campaignList = $this->getDoctrine()->getRepository('BundleAppBundle:Campaign')->findAll();
        
        return $this->render('BundleAppBundle:Dashboard:home.html.twig',array(
            'campaigns'=>$campaignList

        ));
    }
}
