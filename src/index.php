<?php

require_once __DIR__.'/../vendor/autoload.php';

use App\Entity\Campaign;
use App\GoogleApi\GoogleCalendarApi;
use App\Parser\Engine\CampaignParseEngine;
use App\Parser\Parser;

date_default_timezone_set('Asia/Tokyo');

define('GOOGLE_CALENDAR_ID', 'am384g4913d514u6lgdmcv8ces@group.calendar.google.com');
define('GOOGLE_API_SERVICE_ACCOUNT_PATH', __DIR__.'/../service-account.json');
define('GOOGLE_API_SCOPES', [
    Google_Service_Calendar::CALENDAR,
]);

define('TARGET_URL', 'http://pso2.jp/players/news/?charid=i_boostevent');
//define('TARGET_URL', 'http://localhost/tests/resources/pso2.html');

try {
    $calendar = new GoogleCalendarApi(GOOGLE_API_SERVICE_ACCOUNT_PATH, GOOGLE_API_SCOPES);
    $parser = new Parser(new CampaignParseEngine());

    // キャンペーンを取得
    $campaigns = $parser->scrape(TARGET_URL)->data();
    // カレンダーを初期化
    $calendar->clear(GOOGLE_CALENDAR_ID);

    /** @var Campaign $campaign */
    foreach ($campaigns as $campaign) {
        $summary = $campaign->description();
        $start = [
            'dateTime' => $campaign->period()->start()->format(DateTime::ISO8601),
            'timeZone' => $campaign->period()->start()->getTimezone(),
        ];

        // 終了時間が指定されていない場合は30分に指定する
        if (is_null($campaign->period()->end())) {
            $end = [
                'dateTime' => $campaign->period()->start()->add(new DateInterval('PT30M'))->format(DateTime::ISO8601),
                'timeZone' => $campaign->period()->start()->getTimezone(),
            ];
        } else {
            $end = [
                'dateTime' => $campaign->period()->end()->format(DateTime::ISO8601),
                'timeZone' => $campaign->period()->end()->getTimezone(),
            ];
        }

        // カレンダーイベントを作成する
        $event = new Google_Service_Calendar_Event([
            'summary' => $summary,
            'description' => TARGET_URL,
            'start' => $start,
            'end' => $end,
        ]);

        $event = $calendar->insert(GOOGLE_CALENDAR_ID, $event);
    }
} catch (Exception $e) {
    throw $e;
}
