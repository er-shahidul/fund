<?php

namespace Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    public function isFacebookLogin()
    {

        if(!$this->getUser()){
            return  $this->redirect($this->generateUrl('hwi_oauth_service_redirect',array('service'=>'facebook')));
        }
    }
    public function paginate($dql)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        if (is_string($dql)) {
            $query = $em->createQuery($dql);
        } else {
            $query = $dql;
        }

        $paginator = $this->get('knp_paginator');
        $value     = $paginator->paginate(
            $query,
            $page = $this->get('request')->query->get('page', 1) /*page number*/,
            10/*limit per page*/
        );

        return $value;
    }
}
