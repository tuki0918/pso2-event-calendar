<?php

namespace App\Parser\Core;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Crawler;

abstract class ParseEngine implements ParseEngineInterface
{
    /** @var Crawler */
    protected $crawler;
    /** @var CssSelectorConverter */
    protected $cssSelector;
    /** @var LoggerInterface */
    protected $log;
    /** @var Client */
    private $client;
    /** @var null|string */
    private $url;

    /**
     * ParseEngine constructor.
     */
    public function __construct(LoggerInterface $log)
    {
        $this->client = new Client();
        $this->crawler = new Crawler();
        $this->cssSelector = new CssSelectorConverter();
        $this->log = $log;
    }

    /**
     * @param string $content
     * @param null|string $url
     * @return ParseEngine
     */
    public function setContent(string $content, string $url = null): self
    {
        $this->url = $url;
        $this->crawler->clear();
        $this->crawler->addContent($content);
        return $this;
    }

    /**
     * @param string $url
     * @return ParseResponse
     */
    public function scrape(string $url): ParseResponse
    {
        $content = $this->client->request('GET', $url)->getBody();
        return $this->setContent($content, $url)->parse();
    }

    /**
     * @return null|string
     */
    protected function url(): ?string
    {
        return $this->url;
    }
}
