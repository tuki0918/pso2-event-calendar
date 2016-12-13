<?php

require_once __DIR__.'/../vendor/autoload.php';

use App\Entity\Campaign;
use App\GoogleApi\GoogleCalendarApi;
use App\Parser\Engine\CampaignParseEngine;
use App\Parser\Parser;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RavenHandler;
use Monolog\Logger;

date_default_timezone_set('Asia/Tokyo');

define('SENTRY_IO_API', 'https://4afb11d50e0c48cba1240e1d64fdafb1:4d57c7290ec94e61bc41e2509e042399@sentry.io/119834');

define('GOOGLE_CALENDAR_ID', 'am384g4913d514u6lgdmcv8ces@group.calendar.google.com');
define('GOOGLE_API_SERVICE_ACCOUNT_PATH', __DIR__.'/../service-account.json');
define('GOOGLE_API_SCOPES', [
    Google_Service_Calendar::CALENDAR,
]);

define('TARGET_URL', 'http://pso2.jp/players/news/?charid=i_boostevent');
//define('TARGET_URL', 'http://localhost/tests/resources/pso2.html');

$client = new Raven_Client(SENTRY_IO_API);
$handler = new RavenHandler($client);
$handler->setFormatter(new LineFormatter("%message% %context% %extra%\n"));

$log = new Logger('name');
$log->pushHandler($handler);

try {
    $calendar = new GoogleCalendarApi(GOOGLE_API_SERVICE_ACCOUNT_PATH, GOOGLE_API_SCOPES);
    $parser = new Parser(new CampaignParseEngine());

    // キャンペーンを取得
    $campaigns = $parser->scrape(TARGET_URL)->data();
    // カレンダーを初期化
    $calendar->clear(GOOGLE_CALENDAR_ID);

    // イベント変数
    $targetUrl = TARGET_URL;
    $now = new DateTimeImmutable();

    /** @var Campaign $campaign */
    foreach ($campaigns as $campaign) {
        $summary = $campaign->description();
        $description = <<< EOT
{$targetUrl}
Last-Modified: {$now->format('Y-m-d H:i:s')}
EOT;

        $start = [
            'dateTime' => $campaign->period()->start()->format(DateTime::ISO8601),
            'timeZone' => $campaign->period()->start()->getTimezone(),
        ];
        $end = [
            'dateTime' => $campaign->period()->end()->format(DateTime::ISO8601),
            'timeZone' => $campaign->period()->end()->getTimezone(),
        ];

        // カレンダーイベントを作成する
        $event = new Google_Service_Calendar_Event([
            'summary' => $summary,
            'description' => $description,
            'start' => $start,
            'end' => $end,
        ]);

        $event = $calendar->insert(GOOGLE_CALENDAR_ID, $event);
    }
} catch (Exception $e) {
    $log->err($e->getMessage());
}
