<?php

namespace Bundle\AppBundle\Service;

use Guzzle\Http\Client;

class SMSTransporter
{
    private $endPoint;
    private $username;
    private $password;
    private $client;

    public function __construct($endpoint, $username, $password)
    {
        $this->endPoint = $endpoint;
        $this->username = $username;
        $this->password = $password;

        $this->client = new Client();
    }

    public function send($number, $message)
    {
        $param = array(
            'ms'       => $number,
            'txt'      => $message,
            'username' => $this->username,
            'password' => $this->password,
        );

       $request = $this->client->createRequest('POST', $this->buildUrl($param));

        return $request;
    }

    private function buildUrl($param)
    {
        return $this->endPoint . '?' . http_build_query($param);
    }

}