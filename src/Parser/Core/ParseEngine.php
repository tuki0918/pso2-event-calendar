<?php

namespace App\Parser\Core;

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

    /**
     * ParseEngine constructor.
     */
    public function __construct(LoggerInterface $log)
    {
        $this->crawler = new Crawler();
        $this->cssSelector = new CssSelectorConverter();
        $this->log = $log;
    }

    /**
     * @param string $content
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->crawler->clear();
        $this->crawler->addContent($content);
        return $this;
    }
}
