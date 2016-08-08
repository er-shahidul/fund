<?php

namespace Bundle\AppBundle\Twig;

class ReadMoreExtention extends \Twig_Extension
{

    public function getFilters()
    {
        return array('readmore' => new \Twig_Filter_Method($this, 'ReadMore', array('is_safe' => array('html'))));
    }

    public function ReadMore($string, $url,$length)
    {
       
        if(strlen($string)> 40){
            $count=true;
        }
        else $count=false;


        if ($count === false) {
            return $string;
        } else {
            $text = substr($string, 0, $length);
            $string = $text . '<div class="readmore"><a href="' . $url . '">Read More</a></div>';
            return $string;
        }
    }

    public function getName()
    {
        return 'readmore';
    }

}