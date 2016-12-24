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
    public function コンテンツをロードしパースする(CampaignParseEngine $engine)
    {
        $content = file_get_contents(__DIR__.'/../../resources/pso2.html');
        $response = $engine->setContent($content)->parse();
        $this->assertInstanceOf(CampaignResponse::class, $response);
        return $response;
    }

    /**
     * @test
     * @depends コンテンツをロードしパースする
     */
    public function キャンペーンタイトルの確認(CampaignResponse $response)
    {
        $title = '2016/12/7 ～ 12/14のブースト＆予告イベント情報！';
        $this->assertEquals($title, $response->title());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースする
     */
    public function イベント件数の確認(CampaignResponse $response)
    {
        $data = $response->data();
        $this->assertEquals(32, count($data));
    }

    /**
     * @test
     * @depends コンテンツをロードしパースする
     */
    public function イベント内容の確認A(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[0]; // 1つ目
        $this->assertEquals('', $campaign->id()->value());
        $this->assertEquals('2016-12-07 20:00:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2016-12-07 20:30:00', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::EMERGENCY, $campaign->type()->getValue());
        $this->assertEquals('「氷上のメリークリスマス2016」', $campaign->description());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースする
     */
    public function イベント内容の確認B(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[19]; // 20個目
        $this->assertEquals('', $campaign->id()->value());
        $this->assertEquals('2016-12-11 15:00:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2016-12-11 18:00:00', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::CASINO, $campaign->type()->getValue());
        $this->assertEquals('「リーリールーレット」でチャンスマスの出現確率アップ！', $campaign->description());
    }
}