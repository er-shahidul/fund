<?php

namespace Bundle\AppBundle\Controller;

use Bundle\AppBundle\Entity\Campaign;
use Bundle\AppBundle\Form\CampaignType;
use Bundle\UserBundle\Entity\User;
use Bundle\UserBundle\Form\UserType;
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

        $campaign = new Campaign();

        $form = $this->createForm(new CampaignType(), $campaign);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->saveCampaign($campaign);

                $massage = 'Campaign Successfully Inserted';
                $this->get('session')->getFlashBag()->add('notice', $massage);
                return $this->redirect($this->generateUrl('campaign_list'));
            }
        }

        return $this->render(
            'BundleAppBundle:Campaign:form.html.twig',
            array(
                'form'     => $form->createView()
            )
        );
        
    }
    
    public  function saveCampaign(Campaign $campaign) {

        $campaign->setCreatedDate(new \DateTime(date('Y-m-d')));
        $campaign->setCreatedBy($this->getUser());
        $campaignRepo = $this->getDoctrine()->getRepository("BundleAppBundle:Campaign");
        $campaignRepo->create($campaign);
    }

    
}
