<?php

namespace Bundle\AppBundle\Controller\Admin;


use Bundle\AppBundle\Entity\Category;
use Bundle\AppBundle\Entity\Location;
use Bundle\AppBundle\Form\CategoryType;
use Bundle\AppBundle\Form\LocationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LocationController extends Controller
{
    public function indexAction()
    {
        $locationList = $this->getDoctrine()
                ->getRepository('BundleAppBundle:Location')
                ->findAll();

        return $this->render('BundleAppBundle:Admin/Location:home.html.twig',array(
            'LocationList' =>$locationList
        ));

    } 
    public function createAction(Request $request)
    {
        $location = new Location();

        $form = $this->createForm(new LocationType(), $location);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->saveCategory($location);
                return $this->redirect($this->generateUrl('location_list'));
            }
        }

        return $this->render(
            'BundleAppBundle:Admin/Location:form.html.twig',
            array(
                'form'     => $form->createView()
            )
        );
        
    }
    public  function saveCategory(Location $location) {

        $location->setCreatedDate(new \DateTime(date('Y-m-d')));
        $location->setCreatedBy($this->getUser());
        $location->setApproved('Active');
        $locationRepo = $this->getDoctrine()->getRepository("BundleAppBundle:Location");
        $locationRepo->create($location);
    }

    public function changeStatusAction(Location $location){


        if($location->getApproved() === 'Active'){
            $location->setApproved('Inactive');

            $massage = 'Category Successfully Not Approved';
            $this->get('session')->getFlashBag()->add('success', $massage);
        } else{
            $location->setApproved('Active');

            $massage = 'Category Successfully Approved';
            $this->get('session')->getFlashBag()->add('success', $massage);
        }
        $this->getDoctrine()->getRepository('BundleAppBundle:Location')->persist($location);

        return $this->redirect($this->generateUrl('location_list'));
    }
    
}
