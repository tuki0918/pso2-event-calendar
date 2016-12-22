<?php

require_once __DIR__.'/../vendor/autoload.php';

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
define('DEBUG_MODE', (bool)getenv('DEBUG_MODE'));

define('TARGET_URL', DEBUG_MODE ? getenv('TARGET_URL_DEBUG') : getenv('TARGET_URL'));

define('SENTRY_IO_API', getenv('SENTRY_IO_API'));

define('GOOGLE_CALENDAR_ID', getenv('GOOGLE_CALENDAR_ID'));
define('GOOGLE_API_SERVICE_ACCOUNT_PATH', ROOT_DIR.'/service-account.json');
define('GOOGLE_API_SCOPES', [
    Google_Service_Calendar::CALENDAR,
]);

// Task

class Task
{
    /** @var GoogleCalendarApi */
    private $calendar;
    /** @var Parser */
    private $parser;
    /** @var DateTimeImmutable */
    private $now;
    /** @var string */
    private $url;
    /** @var string */
    private $calendarId;

    /**
     * Task constructor.
     * @param Parser $parser
     * @param GoogleCalendarApi $calendar
     */
    public function __construct(Parser $parser, GoogleCalendarApi $calendar)
    {
        $this->parser = $parser;
        $this->calendar = $calendar;
        $this->now = new DateTimeImmutable();
        $this->url = TARGET_URL;
        $this->calendarId = GOOGLE_CALENDAR_ID;
    }

    /**
     * キャンペーン情報を取得し、カレンダーに登録する
     */
    public function run()
    {
        // キャンペーンを取得
        $campaigns = $this->parser->scrape($this->url)->data();
        // カレンダーを初期化
        $this->calendar->clear($this->calendarId);

        /** @var Campaign $campaign */
        foreach ($campaigns as $campaign) {
            // カレンダーイベントを作成する
            $event = $this->createCalendarEvent($campaign);
            $event = $this->calendar->insert($this->calendarId, $event);
        }
    }

    /**
     * @param Campaign $campaign
     * @return Google_Service_Calendar_Event
     */
    private function createCalendarEvent(Campaign $campaign): Google_Service_Calendar_Event
    {
        $description = "{$this->url}".PHP_EOL;
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

// Loggerの設定
$handler = new RavenHandler(new Raven_Client(SENTRY_IO_API));
$handler->setFormatter(new LineFormatter("%message% %context% %extra%\n"));

$log = new Logger(DEBUG_MODE ? 'debug' : 'production');
$log->pushHandler($handler);

// 依存クラスの設定
$calendar = new GoogleCalendarApi(GOOGLE_API_SERVICE_ACCOUNT_PATH, GOOGLE_API_SCOPES);
$parser = new Parser(new CampaignParseEngine($log));

// タスクの呼び出し
(new Task($parser, $calendar))->run();
