<?php

namespace Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    { 
        return $this->render('BundleAppBundle:Dashboard:home.html.twig');
    }
}
