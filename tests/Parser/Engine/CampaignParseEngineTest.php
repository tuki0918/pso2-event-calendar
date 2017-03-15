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
        $content = file_get_contents(__DIR__.'/../../resources/campaign-20161207.html');
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
        $title = '2016/12/7 ～ 12/14のブースト＆予告イベント情報！';
        $this->assertEquals($title, $response->title());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするA
     */
    public function イベント件数の確認A(CampaignResponse $response)
    {
        $data = $response->data();
        $this->assertEquals(32, count($data));
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするA
     */
    public function イベント内容の確認A1(CampaignResponse $response)
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
     * @depends コンテンツをロードしパースするA
     */
    public function イベント内容の確認A20(CampaignResponse $response)
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

    /**
     * @test
     * @depends パーサーを作成できる
     */
    public function コンテンツをロードしパースするB(CampaignParseEngine $engine)
    {
        $content = file_get_contents(__DIR__.'/../../resources/campaign-20161221.html');
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
        $title = '2016/12/21 ～ 12/28のブースト＆予告イベント情報！';
        $this->assertEquals($title, $response->title());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするB
     */
    public function イベント件数の確認B(CampaignResponse $response)
    {
        $data = $response->data();
        $this->assertEquals(44, count($data));
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするB
     */
    public function イベント内容の確認B13(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[12]; // 13個目
        $this->assertEquals('', $campaign->id()->value());
        $this->assertEquals('2016-12-24 00:00:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2016-12-24 23:59:59', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::BOOST, $campaign->type()->getValue());
        $this->assertEquals('すべてのクエストに対してレアドロップの倍率が＋50％！', $campaign->description());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするB
     */
    public function イベント内容の確認B18(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[17]; // 18個目
        $this->assertEquals('', $campaign->id()->value());
        $this->assertEquals('2016-12-24 20:00:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2016-12-24 20:30:00', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::LIVE, $campaign->type()->getValue());
        $this->assertEquals('クーナスペシャルライブ「Our Fighting」', $campaign->description());
    }

    /**
     * @test
     * @depends パーサーを作成できる
     */
    public function コンテンツをロードしパースするC(CampaignParseEngine $engine)
    {
        $content = file_get_contents(__DIR__.'/../../resources/campaign-20161228.html');
        $response = $engine->setContent($content)->parse();
        $this->assertInstanceOf(CampaignResponse::class, $response);
        return $response;
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするC
     */
    public function キャンペーンタイトルの確認C(CampaignResponse $response)
    {
        $title = '2016/12/28 ～ 2017/1/11のブースト＆予告イベント情報！';
        $this->assertEquals($title, $response->title());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするC
     */
    public function イベント件数の確認C(CampaignResponse $response)
    {
        $data = $response->data();
        $this->assertEquals(113, count($data));
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするC
     */
    public function イベント内容の確認C1(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[0]; // 1個目
        $this->assertEquals('', $campaign->id()->value());
        $this->assertEquals('2016-12-28 20:00:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2016-12-28 20:30:00', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::EMERGENCY, $campaign->type()->getValue());
        $this->assertEquals('「月駆ける幻創の母」', $campaign->description());
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするC
     */
    public function イベント内容の確認C72(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[71]; // 72個目
        $this->assertEquals('', $campaign->id()->value());
        $this->assertEquals('2017-01-05 07:00:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-01-05 09:00:00', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::ARKSLEAGUE, $campaign->type()->getValue());
        $this->assertEquals('アークスリーグ開催！アイテム収集：ウェポンズバッヂ2016銀', $campaign->description());
    }

    /**
     * @test
     * @depends パーサーを作成できる
     */
    public function コンテンツをロードしパースするD(CampaignParseEngine $engine)
    {
        $content = file_get_contents(__DIR__.'/../../resources/campaign-20170315.html');
        $response = $engine->setContent($content)->parse();
        $this->assertInstanceOf(CampaignResponse::class, $response);
        return $response;
    }

    /**
     * @test
     * @depends コンテンツをロードしパースするD
     */
    public function イベント内容の確認D2(CampaignResponse $response)
    {
        $data = $response->data();
        /** @var Campaign $campaign */
        $campaign = $data[1]; // 2個目
        $this->assertEquals('', $campaign->id()->value());
        $this->assertEquals('2017-03-15 21:00:00', $campaign->period()->start()->format('Y-m-d H:i:s'));
        $this->assertEquals('2017-03-15 23:00:00', $campaign->period()->end()->format('Y-m-d H:i:s'));
        $this->assertEquals(CampaignType::EVENTREWARD, $campaign->type()->getValue());
        $this->assertEquals('カジノスーパーブースト実施中！', $campaign->description());
    }
}
