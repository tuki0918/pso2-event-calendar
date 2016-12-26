<?php

namespace App\Parser\Core;

interface ParseEngineInterface
{
    /**
     * @param string $url
     * @param string $content
     * @return self
     */
    public function setContent(string $url, string $content): ParseEngine;

    /**
     * @return mixed
     */
    public function parse();
}
