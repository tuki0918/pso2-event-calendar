<?php

namespace App\Parser\Engine;

use App\Entity\Campaign;
use App\Entity\Enum\CampaignType;
use App\Entity\ValueObject\CampaignId;
use App\Entity\ValueObject\CampaignPeriod;
use App\Parser\Core\ParseEngine;
use App\Parser\Response\CampaignResponse;
use DateTimeImmutable;
use LogicException;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

class CampaignParseEngine extends ParseEngine
{
    /** @var Campaign[] */
    private $data;
    /** @var DateTimeImmutable|null */
    private $previous;

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
     * @param DateTimeImmutable $day
     */
    private function setPrevious(DateTimeImmutable $day)
    {
        $this->previous = $day;
    }

    /**
     * @return DateTimeImmutable|null
     */
    private function previous(): ?DateTimeImmutable
    {
        return $this->previous;
    }

    /**
     * キャンペーンタイトル（期間）
     * @return string
     */
    private function title(): string
    {
        // e.g.) 2017/08/02～2017/08/09
        $nodes = $this->crawler->filterXPath(
            $this->cssSelector->toXPath('li.pager--date')
        );

        if ($nodes->count() > 0) {
            return $nodes->text();
        }

        throw new LogicException('レイアウトが変更されている可能性があります。');
    }

    /**
     * キャンペーンデータを取得
     * @return Campaign[]
     */
    private function campaigns(): array
    {
        $this->clear();
        $nodes = $this->crawler->filterXPath(
            $this->cssSelector->toXPath('div.eventTable--event table > tr > td > div > dl')
        );

        if ($nodes->count() > 0) {
            $nodes->each(function (Crawler $node, $i) {
                // キャンペーンを登録
                $campaign = Campaign::create(
                    CampaignId::create($i + 1),
                    $this->period($node),
                    $this->type($node),
                    $this->description($node),
                    $this->url()
                );

                // TODO: 重複は追加しない
                $this->addCampaign($campaign);
            });
        }

        return $this->data();
    }

    /**
     * キャンペーン期間
     * @deprecated DOM構造の変更により意図しない値を返す可能性あり
     * @param Crawler $node
     * @return CampaignPeriod
     */
    private function period(Crawler $node): CampaignPeriod
    {
        // e.g.) 08/1123:30～08/1200:00
        $period = trim($node->filterXPath($this->cssSelector->toXPath('dd'))->first()->text());

        $regex = '`^(?<smounth>\d{2})/(?<sday>\d{2})(?<shour>\d{2}):(?<sminute>\d{2})～(?<emounth>\d{2})/(?<eday>\d{2})(?<ehour>\d{2}):(?<eminute>\d{2})`';
        if (preg_match($regex, $period, $m)) {
            $year = $this->startDate()->format('Y');
            $startAt = new DateTimeImmutable(
                date('Y-m-d H:i:s', mktime($m['shour'], $m['sminute'], 0, $m['smounth'], $m['sday'], $year))
            );
            // TODO: 年度調整
            $this->setPrevious($startAt);

            $endAt = new DateTimeImmutable(
                date('Y-m-d H:i:s', mktime($m['ehour'], $m['eminute'], 0, $m['emounth'], $m['eday'], $startAt->format('Y')))
            );

            // TODO: 年度調整
            return CampaignPeriod::create($startAt, $endAt);
        } else {
            $this->log->warning('period parse error.', [
                'content' => $period,
            ]);
            throw new RuntimeException('日付パースエラー');
        }
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
            $this->cssSelector->toXPath('dt')
        )->first()->text());

        switch ($content) {
            case '緊急':
                return CampaignType::Emergency();
            case 'カジノイベント':
                return CampaignType::Casino();
            case 'チャレンジ':
                return CampaignType::Challenge();
            case 'ライブ':
                return CampaignType::Live();
            case 'イベント報酬':
                return CampaignType::EventReward();
            case 'アークスリーグ':
                return CampaignType::ArksLeague();
            case 'パネル報酬':
                return CampaignType::Unknown();
            case 'PSO2の日':
                return CampaignType::Unknown();
            case 'キャンペーン':
                return CampaignType::Unknown();
            case 'ブースト':
                return CampaignType::Boost();
            case 'ネットカフェ':
                return CampaignType::NetCafe();
            case '公式番組情報':
                return CampaignType::Unknown();
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
            $this->cssSelector->toXPath('dd')
        )->last()->text());
    }

    /**
     * イベント開始日
     * @deprecated DOM構造の変更により意図しない値を返す可能性あり
     * @param Crawler $node
     * @return DateTimeImmutable
     */
    private function startDate(): DateTimeImmutable
    {
        $title = $this->title();
        $regex = '`^(?<year>\d{4})/(?<mounth>\d{2})/(?<day>\d{2})`';
        if (preg_match($regex, $title, $m)) {
            $time = date('Y-m-d 00:00:00', mktime(0, 0, 0, $m['mounth'], $m['day'], $m['year']));
            return new DateTimeImmutable($time);
        } else {
            throw new RuntimeException('イベント開始日パースエラー');
        }
    }
}
