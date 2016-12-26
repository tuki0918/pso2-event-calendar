<?php

namespace App\Tests\Entity;

use App\Entity\Campaign;
use App\Entity\Enum\CampaignType;
use App\Entity\ValueObject\CampaignId;
use App\Entity\ValueObject\CampaignPeriod;

class CampaignTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function キャンペーンを作成できる()
    {
        $id = $this->createMock(CampaignId::class);
        $period = $this->createMock(CampaignPeriod::class);
        $type = $this->createMock(CampaignType::class);
        $description = 'string string string string.';
        $link = 'http://localhost';

        $obj = Campaign::create($id, $period, $type, $description, $link);

        $this->assertInstanceOf(Campaign::class, $obj);
        $this->assertInstanceOf(CampaignId::class, $obj->id());
        $this->assertInstanceOf(CampaignPeriod::class, $obj->period());
        $this->assertInstanceOf(CampaignType::class, $obj->type());
        $this->assertEquals($description, $obj->description());
        $this->assertEquals($link, $obj->link());
    }
}
