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
    /** @var string */
    private $calendarId;
    /** @var string */
    private $calendarCreator;

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
        $this->calendarCreator = GOOGLE_CALENDAR_CREATOR;
    }

    /**
     * キャンペーン情報を取得し、カレンダーに登録する
     * @param string $url
     * @param string $calendarId
     */
    public function run(string $url, string $calendarId)
    {
        // キャンペーンを取得
        $campaigns = $this->engine->scrape($url)->data();
        // カレンダーを初期化
        $this->calendar->clear($calendarId, $this->calendarCreator);

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
