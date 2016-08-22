<?php

namespace Bundle\AppBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SMSTransporter
{
    protected $objClient;

    protected $message;

    protected $phoneNumber;

    protected $baseUrl = "http://divinesms.prismerp.net/";

    /**
     * @return self
     */
    public function setClient()
    {
        $this->objClient = new Client(array(
            'cookies' => true,
            'base_uri' => $this->baseUrl,
        ));
        return $this;
    }


    /**
     * @return mixed
     */
    public function getObjClient()
    {
        return $this->objClient;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     * @return self
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function send() {

        $response = $this->getCookie();

        $arrSetCookie = $response->getHeader('Set-Cookie');

        $arrCookieInfo =  explode(';', $arrSetCookie[0]);
        $arrCookie = array_filter(explode('csrftoken=', $arrCookieInfo[0]));
        $strCookie = $arrCookie[1];

        $post_data = [
            'headers' => ['X-CSRFToken' => $strCookie, 'Referer' => $this->baseUrl.'/sms/'],
            'form_params' => [
                'access_token' => 'sxbqmsrxxcmqtsdc',
                'source' =>   'rightbrain',
                'priority' => 1,
                'receiver' => '+88'.$this->getPhoneNumber(),
                'message' => $this->getMessage()
            ],

            'cookie' => ['csrftoken' => $strCookie ]
        ];

        try{

            $objResponse = $this->objClient->request('POST', '/sms/send/', $post_data);
        } catch (RequestException $e) {
            echo $e->getMessage();
        }
        return $objResponse;
    }

    public function getCookie(){

        try{
            $objResponse = $this->objClient->get('/sms/');
        } catch (RequestException $e) {
            echo $e->getMessage();
        }

        return $objResponse;
    }

}