<?php

namespace App\Task;

use App\Entity\Campaign;
use App\GoogleApi\GoogleCalendarApiInterface;
use App\Parser\Core\ParseEngineInterface;
use App\Parser\Engine\CampaignParseEngine;
use DateTime;
use DateTimeImmutable;
use Google_Service_Calendar_Event;

class Calendar
{
    /** @var ParseEngineInterface */
    private $engine;
    /** @var GoogleCalendarApiInterface */
    private $calendar;
    /** @var DateTimeImmutable */
    private $now;

    /**
     * Calendar constructor.
     * @param CampaignParseEngine $engine
     * @param GoogleCalendarApiInterface $calendar
     */
    public function __construct(
        CampaignParseEngine $engine,
        GoogleCalendarApiInterface $calendar
    ) {
        $this->engine = $engine;
        $this->calendar = $calendar;
        $this->now = new DateTimeImmutable();
    }

    /**
     * キャンペーン情報を取得する
     * @param string $url
     * @return Campaign[]
     */
    public function get(string $url): array
    {
        // キャンペーンを取得
        return $this->engine->scrape($url)->data();
    }

    /**
     * カレンダーに反映する
     * @param string $calendarId
     * @param string $calendarCreator
     * @param Campaign[] ...$campaigns
     */
    public function deploy(
        string $calendarId,
        string $calendarCreator,
        Campaign ...$campaigns
    ) {
        // カレンダーを初期化
        $this->calendar->clear($calendarId, $calendarCreator);

        /** @var Campaign $campaign */
        foreach ($campaigns as $campaign) {
            // カレンダーイベントを作成する
            $event = $this->createCalendarEvent($campaign);
            $event = $this->calendar->insert($calendarId, $event);
        }
    }

    /**
     * @param Campaign $campaign
     * @return Google_Service_Calendar_Event
     */
    private function createCalendarEvent(Campaign $campaign): Google_Service_Calendar_Event
    {
        $description = "{$campaign->link()}".PHP_EOL;
        $description.= "Last-Modified: {$this->now->format('Y-m-d H:i:s')}".PHP_EOL;

        return new Google_Service_Calendar_Event([
            'summary' => $campaign->description(),
            'description' => $description,
            'start' => [
                'dateTime' => $campaign->period()->start()->format(DateTime::ISO8601),
                'timeZone' => $campaign->period()->start()->getTimezone(),
            ],
            'end' => [
                'dateTime' => $campaign->period()->end()->format(DateTime::ISO8601),
                'timeZone' => $campaign->period()->end()->getTimezone(),
            ],
        ]);
    }
}
