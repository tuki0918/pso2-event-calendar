<?php

namespace App\Tests\Entity\Enum;

use App\Entity\Enum\CampaignType;
use Guzzle\Common\Exception\BadMethodCallException;

class CampaignTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function ブーストイベント()
    {
        $obj = CampaignType::Boost();
        $this->assertInstanceOf(CampaignType::class, $obj);
        $this->assertEquals($obj->getValue(), CampaignType::BOOST);
    }

    /**
     * @test
     */
    public function 予告緊急クエスト()
    {
        $obj = CampaignType::Emergency();
        $this->assertInstanceOf(CampaignType::class, $obj);
        $this->assertEquals($obj->getValue(), CampaignType::EMERGENCY);
    }

    /**
     * @test
     */
    public function アークスリーグ()
    {
        $obj = CampaignType::ArksLeague();
        $this->assertInstanceOf(CampaignType::class, $obj);
        $this->assertEquals($obj->getValue(), CampaignType::ARKSLEAGUE);
    }

    /**
     * @test
     */
    public function ネットカフェ()
    {
        $obj = CampaignType::NetCafe();
        $this->assertInstanceOf(CampaignType::class, $obj);
        $this->assertEquals($obj->getValue(), CampaignType::NETCAFE);
    }

    /**
     * @test
     */
    public function チャレンジマイルアップ()
    {
        $obj = CampaignType::Challenge();
        $this->assertInstanceOf(CampaignType::class, $obj);
        $this->assertEquals($obj->getValue(), CampaignType::CHALLENGE);
    }

    /**
     * @test
     */
    public function カジノ()
    {
        $obj = CampaignType::Casino();
        $this->assertInstanceOf(CampaignType::class, $obj);
        $this->assertEquals($obj->getValue(), CampaignType::CASINO);
    }

    /**
     * @test
     */
    public function ライブイベント()
    {
        $obj = CampaignType::Live();
        $this->assertInstanceOf(CampaignType::class, $obj);
        $this->assertEquals($obj->getValue(), CampaignType::LIVE);
    }

    /**
     * @test
     */
    public function 未実装()
    {
        $obj = CampaignType::Unknown();
        $this->assertInstanceOf(CampaignType::class, $obj);
        $this->assertEquals($obj->getValue(), CampaignType::UNKNOWN);
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function 未定義関数エラー()
    {
        $obj = CampaignType::Dummy();
    }
}
