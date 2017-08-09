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

    /**
     * @test
     * @depends パーサーを作成できる
     */
    public function コンテンツをロードしパースするA(CampaignParseEngine $engine)
    {
        $content = file_get_contents(__DIR__.'/../../resources/campaign-20170802.html');
        $response = $engine->setContent($content)->parse();
        $this->assertInstanceOf(CampaignResponse::class, $response);
        return $response;
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするA
     */
    public function キャンペーンタイトルの確認A(CampaignResponse $response)
    {
        $title = '2017/08/02～2017/08/09';
        $this->assertEquals($title, $response->title());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするA
     */
    public function イベント件数の確認A(CampaignResponse $response)
    {
        $data = $response->data();
        // 重複1つあり
        $this->assertEquals(42, count($data));
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするA
     */
    public function イベント内容の確認A1(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[0];
        $this->assertEquals(1, $campaign->id()->value());
        $this->assertEquals('2017-08-02 02:00:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-08-02 02:30:00', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::EMERGENCY, $campaign->type()->getValue());
        $this->assertEquals('新世を成す幻創の造神', $campaign->description());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするA
     */
    public function イベント内容の確認A36(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[35];
        $this->assertEquals(36, $campaign->id()->value());
        $this->assertEquals('2017-08-05 22:00:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-08-05 23:30:00', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::UNKNOWN, $campaign->type()->getValue());
        $this->assertEquals('PSO2 アークスライブ！ワンモア！', $campaign->description());
    }


    /**
     * @test
     * @depends パーサーを作成できる
     */
    public function コンテンツをロードしパースするB(CampaignParseEngine $engine)
    {
        $content = file_get_contents(__DIR__.'/../../resources/campaign-20170809.html');
        $response = $engine->setContent($content)->parse();
        $this->assertInstanceOf(CampaignResponse::class, $response);
        return $response;
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするB
     */
    public function キャンペーンタイトルの確認B(CampaignResponse $response)
    {
        $title = '2017/08/09～2017/08/16';
        $this->assertEquals($title, $response->title());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするB
     */
    public function イベント件数の確認B(CampaignResponse $response)
    {
        $data = $response->data();
        $this->assertEquals(41, count($data));
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするB
     */
    public function イベント内容の確認B19(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[18];
        $this->assertEquals(19, $campaign->id()->value());
        $this->assertEquals('2017-08-14 13:00:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-08-14 13:30:00', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::LIVE, $campaign->type()->getValue());
        $this->assertEquals('クーナスペシャルライブ「Cosmic twinkle star」', $campaign->description());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするB
     */
    public function イベント内容の確認B22(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[21];
        $this->assertEquals(22, $campaign->id()->value());
        $this->assertEquals('2017-08-14 13:30:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-08-14 14:00:00', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::EMERGENCY, $campaign->type()->getValue());
        $this->assertEquals('ビーチウォーズ２０１７！', $campaign->description());
    }
}
