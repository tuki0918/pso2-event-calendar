<?php

namespace App\GoogleApi;

use Google_Client;

class GoogleApi
{
    /** @var Google_Client */
    private $client;

    /**
     * GoogleApi constructor.
     * @param Google_Client $client
     */
    public function __construct(Google_Client $client)
    {
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
