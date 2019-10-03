<?php

declare(strict_types = 1);

namespace App\Client;

use Symfony\Component\HttpFoundation\Session\Session;

class GoogleClient
{
    /**
     * @var \Google_Client
     */
    protected $client;
    protected $session;

    protected $counter;

    /**
     * GoogleClient constructor.
     *
     * @param $session
     */
    public function __construct()
    {
        $this->session = new Session();
        $this->client = new \Google_Client();
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/booking/code/';
        $this->client->setAccessType('offline');
        $this->client->setAuthConfig(__DIR__ . '/../../credentials.json');
        $this->client->setRedirectUri($redirect_uri);
        $this->client->addScope(\Google_Service_Calendar::CALENDAR);
        $this->client->setIncludeGrantedScopes(true);
    }

    /**
     * @return \Google_Client
     */
    public function getClient(): \Google_Client
    {
        return $this->client;
    }

    public function getAccessToken()
    {
        if (!$this->session->get('access_token')) {
            return null;
        }
        return $this->session->get('access_token');
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function setAcccessToken($code)
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->session->set('access_token', $accessToken);

    }


}
