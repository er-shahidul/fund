<?php

namespace Bundle\AppBundle\Controller;

use Services_Twilio_RestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CampaignController extends Controller
{
    public function indexAction()
    {
        return $this->render('BundleAppBundle:Campaign:home.html.twig');
    } 
    public function createAction(Request $request)
    {
        
        return $this->render('BundleAppBundle:Campaign:form.html.twig');
        
    }
}
