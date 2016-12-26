<?php

namespace App\Parser\Engine;

use App\Entity\Campaign;
use App\Entity\Enum\CampaignType;
use App\Entity\ValueObject\CampaignId;
use App\Entity\ValueObject\CampaignPeriod;
use App\Parser\Core\ParseEngine;
use App\Parser\Response\CampaignResponse;
use DateInterval;
use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

class CampaignParseEngine extends ParseEngine
{
    /** @var Campaign[] */
    private $data;

    /**
     * @return CampaignResponse
     */
    public function parse(): CampaignResponse
    {
        $campaigns = $this->campaigns();
        return CampaignResponse::create(
            $this->title(),
            ...$campaigns
        );
    }

    /**
     * 初期化
     */
    private function clear(): void
    {
        $this->data = [];
    }

    /**
     * @param Campaign $campaign
     */
    private function addCampaign(Campaign $campaign)
    {
        $this->data[] = $campaign;
    }

    /**
     * @return Campaign[]
     */
    private function data(): array
    {
        return $this->data;
    }

    /**
     * キャンペーンタイトル
     * @return string
     */
    private function title(): string
    {
        // e.g.) 2016/12/7 ～ 12/14のブースト＆予告イベント情報
        return trim($this->crawler->filterXPath(
        // キャンペーンタイトル
            $this->cssSelector->toXPath('div.tabsWrap > dl > dd')
        )->text());
    }

    /**
     * キャンペーンデータを取得
     * @return Campaign[]
     */
    private function campaigns(): array
    {
        $this->clear();
        $this->crawler->filterXPath(
            // キャンペーンテーブル
            $this->cssSelector->toXPath('div.tableWrap')
        )->each(function (Crawler $node) {
            // 日付テキストを取得
            $day = $this->day(trim($node->previousAll()->text()));
            if (is_null($day)) {
                return null;
            }
            // キャンペーン情報リストを取得
            $node->filterXPath(
                // キャンペーンテーブルの行
                $this->cssSelector->toXPath('table tr')
            )->each(function (Crawler $n, $i) use ($day) {
                // 見出し行をスキップする
                if ($i <= 0) {
                    return null;
                }
                // キャンペーンを登録
                $campaign = Campaign::create(
                    CampaignId::create(null),
                    $this->period($n, $day),
                    $this->type($n),
                    $this->description($n),
                    $this->url()
                );
                $this->addCampaign($campaign);
            });
        });
        return $this->data();
    }

    /**
     * キャンペーン開始日
     * @param string $day
     * @return DateTimeImmutable|null
     * @throws RuntimeException
     */
    private function day(string $day): ?DateTimeImmutable
    {
        if ($day === '期間ブースト') {
            return null;
        }

        $regex = '`^(?<mounth>\d+)月(?<day>\d+)日`';
        if (preg_match($regex, $day, $m)) {
            $time = date('Y-m-d 00:00:00', mktime(0, 0, 0, $m['mounth'], $m['day']));
            return new DateTimeImmutable($time);
        } else {
            throw new RuntimeException('日付パースエラー');
        }
    }

    /**
     * キャンペーン期間
     * @deprecated DOM構造の変更により意図しない値を返す可能性あり
     * @param Crawler $node
     * @param DateTimeImmutable $day 開始日
     * @return CampaignPeriod
     */
    private function period(Crawler $node, DateTimeImmutable $day): CampaignPeriod
    {
        $time = trim($node->filterXPath(
            // キャンペーン開始時刻
            $this->cssSelector->toXPath('th.sub')
        )->first()->text());

        $startAt = null;
        $endAt = null;

        $regex1 = '`^(?<shour>\d+):(?<sminute>\d+)$`';
        $regex2 = '`^(?<shour>\d+):(?<sminute>\d+) ～ (?<ehour>\d+):(?<eminute>\d+)$`';
        $regex3 = '`^終日$`';
        if (preg_match($regex1, $time, $m)) {
            $startAt = $day->setTime($m['shour'], $m['sminute']);
            // 終了時間が指定されていない場合は開始時間の30分後に設定する
            $endAt = $startAt->add(new DateInterval('PT30M'));
        } elseif (preg_match($regex2, $time, $m)) {
            $startAt = $day->setTime($m['shour'], $m['sminute']);
            $endAt = $day->setTime($m['ehour'], $m['eminute']);
        } elseif (preg_match($regex3, $time, $m)) {
            $startAt = $day->setTime(0, 0);
            $endAt = $day->setTime(23, 59, 59);
        }

        if (is_null($startAt)) {
            $this->log->warning('period parse error.', [
                'content' => $time,
            ]);
        }

        return CampaignPeriod::create($startAt, $endAt);
    }

    /**
     * キャンペーンタイプ
     * @deprecated DOM構造の変更により意図しない値を返す可能性あり
     * @param Crawler $node
     * @return CampaignType
     */
    private function type(Crawler $node): CampaignType
    {
        $content = trim($node->filterXPath(
            // キャンペーンの分類アイコン
            $this->cssSelector->toXPath('td.icon > img')
        )->first()->attr('alt'));

        switch ($content) {
            case 'ブースト':
                return CampaignType::Boost();
            case '緊急':
                return CampaignType::Emergency();
            case 'アークスリーグ':
                return CampaignType::ArksLeague();
            case 'ネットカフェ':
                return CampaignType::NetCafe();
            case 'カジノ':
                return CampaignType::Casino();
            case 'チャレンジ':
                return CampaignType::Challenge();
            case 'ライブ':
                return CampaignType::Live();
            default:
                $this->log->warning('not found type.', [
                    'content' => $content,
                ]);
                return CampaignType::Unknown();
        }
    }

    /**
     * キャンペーン内容
     * @deprecated DOM構造の変更により意図しない値を返す可能性あり
     * @param Crawler $node
     * @return string
     */
    private function description(Crawler $node): string
    {
        return trim($node->filterXPath(
            // キャンペーン内容
            $this->cssSelector->toXPath('td')
        )->last()->text());
    }
}
