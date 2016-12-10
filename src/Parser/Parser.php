<?php

namespace App\Parser;

use App\Parser\Core\ParseEngine;
use App\Parser\Core\ParseResponse;
use GuzzleHttp\Client;

class Parser
{
    /** @var Client */
    private $client;
    /** @var ParseEngine */
    private $engine;

    /**
     * Parser constructor.
     * @param ParseEngine $engine
     */
    public function __construct(ParseEngine $engine)
    {
        $this->client = new Client();
        $this->engine = $engine;
    }

    /**
     * @param string $url
     * @return ParseResponse
     */
    public function scrape(string $url): ParseResponse
    {
        $response = $this->client->request('GET', $url);
        return $this->engine->setContent(
            $response->getBody()
        )->parse();
    }
}
