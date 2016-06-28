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
}
