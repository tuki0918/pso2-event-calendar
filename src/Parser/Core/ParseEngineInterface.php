<?php

namespace App\Parser\Core;

interface ParseEngineInterface
{
    /**
     * @param string $content
     * @return self
     */
    public function setContent(string $content): ParseEngine;

    /**
     * @return mixed
     */
    public function parse();
}
