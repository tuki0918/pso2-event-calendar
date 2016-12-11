<?php

namespace App\GoogleApi;

use Google_Client;

class GoogleApi
{
    /** @var Google_Client */
    private $client;

    /**
     * GoogleApi constructor.
     * @param string $credential
     * @param array $scopes
     */
    public function __construct(string $credential, array $scopes)
    {
        $client = new Google_Client();
        $client->setAuthConfig($credential);
        $client->setScopes($scopes);

        $this->client = $client;
    }

    /**
     * @return Google_Client
     */
    protected function client(): Google_Client
    {
        return $this->client;
    }
}
