<?php

namespace App\Tests\Parser\Engine;

use App\Entity\Campaign;
use App\Entity\Enum\CampaignType;
use App\Parser\Engine\CampaignParseEngine;
use App\Parser\Response\CampaignResponse;
use Monolog\Logger;

class CampaignParseEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function パーサーを作成できる()
    {
        $log = $this->createMock(Logger::class);
        $engine = new CampaignParseEngine($log);
        return $engine;
    }
}
