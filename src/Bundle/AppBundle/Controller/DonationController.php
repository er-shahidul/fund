<?php

namespace Bundle\AppBundle\Controller;

use Bundle\AppBundle\Entity\Campaign;
use Bundle\AppBundle\Entity\Donation;
use Bundle\AppBundle\Form\CampaignSearchType;
use Bundle\AppBundle\Form\DonationType;
use Omnipay\Common\CreditCard;
use Symfony\Component\HttpFoundation\Request;
use Omnipay\Omnipay;

class DonationController extends BaseController
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
    public function createAction(Request $request, Campaign $campaign)
    {
        $campaignGallary = $this->getDoctrine()
            ->getRepository('BundleAppBundle:CampaignFile')
            ->findBy(array('campaign'=>$campaign));

        $donation  = new Donation();
        $form = $this->createForm(new DonationType(), $donation);
        
        if ('POST' == $request->getMethod()) {
            
            $form->handleRequest($request);

            if ($form->isValid()) {
               // $this->testPaymentGateWay();die;
                $this->saveDonation($donation,$campaign);

                return $this->redirect($this->generateUrl('bundle_app_homepage'));
            }
        }
        
        return $this->render(
            'BundleAppBundle:Donation:donationForm.html.twig',
            array(
                'form'     => $form->createView(),
                'campaign' => $campaign,
                'campaignPhoto' =>$campaignGallary
            )
        );

    }
    public  function saveDonation(Donation $donation , Campaign $campaign) {

        if($this->getUser()){

            $donation->setCreatedBy($this->getUser());
        }
        $donation->setCreatedDate(new \DateTime(date('Y-m-d h:i:s')));
        $donation->setCampaign($campaign);
        $donationRepo = $this->getDoctrine()->getRepository("BundleAppBundle:Donation");
        $donationRepo->create($donation);
    }

    public function testPaymentGateWay()
    {
        $formInputData = array(
            'firstName' => 'Bobby',
            'lastName' => 'Tables',
            'number' => '4111111111111111',
        );
        $card = new CreditCard($formInputData);
        $card->setFirstName('Bobby');
        $gateway = Omnipay::create('PayPal_Express');
        $response = $gateway->purchase(
            [
                'amount' => '10.00',
                'currency' => 'USD',
                'card' => $card,
                'returnUrl' => 'http://fund.local/gateways/PayPal_Express/completePurchase',
                'cancelUrl' => 'http://fund.local/gateways/PayPal_Express/purchase',
            ]
        )->send();

    }

}
