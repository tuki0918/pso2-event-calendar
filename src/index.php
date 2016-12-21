<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\Campaign;
use App\GoogleApi\GoogleCalendarApi;
use App\Parser\Engine\CampaignParseEngine;
use App\Parser\Parser;
use Dotenv\Dotenv;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RavenHandler;
use Monolog\Logger;

date_default_timezone_set('Asia/Tokyo');

// Environment Load
(new Dotenv(__DIR__))->load();

define('ROOT_DIR', __DIR__);
define('DEBUG_MODE', getenv('DEBUG_MODE'));

define('TARGET_URL', DEBUG_MODE ? getenv('TARGET_URL_DEBUG') : getenv('TARGET_URL'));

define('SENTRY_IO_API', getenv('SENTRY_IO_API'));

define('GOOGLE_CALENDAR_ID', getenv('GOOGLE_CALENDAR_ID'));
define('GOOGLE_API_SERVICE_ACCOUNT_PATH', ROOT_DIR . '/service-account.json');
define('GOOGLE_API_SCOPES', [
    Google_Service_Calendar::CALENDAR,
]);

$client = new Raven_Client(SENTRY_IO_API);
$handler = new RavenHandler($client);
$handler->setFormatter(new LineFormatter("%message% %context% %extra%\n"));

$log = new Logger(DEBUG_MODE ? 'debug' : 'production');
$log->pushHandler($handler);

try {
    $calendar = new GoogleCalendarApi(GOOGLE_API_SERVICE_ACCOUNT_PATH, GOOGLE_API_SCOPES);
    $parser = new Parser(new CampaignParseEngine($log));

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
