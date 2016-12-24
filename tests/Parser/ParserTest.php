<?php

namespace App\Tests\Parser;

use App\Parser\Core\ParseEngine;
use App\Parser\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function インスタンスを作成できる()
    {
        $engine = $this->createMock(ParseEngine::class);
        $obj = new Parser($engine);

        $this->assertInstanceOf(Parser::class, $obj);
    }
}
