<?php

use App\GoogleApi\GoogleCalendarApi;
use App\GoogleApi\GoogleCalendarApiInterface;
use Dotenv\Dotenv;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RavenHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

use function DI\factory;

date_default_timezone_set('Asia/Tokyo');

/**
 * Environment Load
 */
(new Dotenv(__DIR__))->load();

/**
 * Constant Define
 */
define('ROOT_DIR', __DIR__);
define('DEBUG_MODE', (bool)getenv('DEBUG_MODE'));
define('TARGET_URL', DEBUG_MODE ? getenv('TARGET_URL_DEBUG') : getenv('TARGET_URL'));
define('SENTRY_IO_API', getenv('SENTRY_IO_API'));
define('GOOGLE_CALENDAR_ID', getenv('GOOGLE_CALENDAR_ID'));
define('GOOGLE_CALENDAR_CREATOR', getenv('GOOGLE_CALENDAR_CREATOR'));
define('GOOGLE_API_SERVICE_ACCOUNT_PATH', ROOT_DIR.'/service-account.json');
define('GOOGLE_API_SCOPES', [
    Google_Service_Calendar::CALENDAR,
]);

/**
 * Return Injection
 */
return [
    GoogleCalendarApiInterface::class => factory(function () {
        $client = new Google_Client();
        $client->setAuthConfig(GOOGLE_API_SERVICE_ACCOUNT_PATH);
        $client->setScopes(GOOGLE_API_SCOPES);
        return new GoogleCalendarApi($client);
    }),
    LoggerInterface::class => factory(function () {
        $handler = new RavenHandler(new Raven_Client(SENTRY_IO_API));
        $handler->setFormatter(new LineFormatter("%message% %context% %extra%\n"));
        $logger = new Logger(DEBUG_MODE ? 'debug' : 'production');
        $logger->pushHandler($handler);
        return $logger;
    }),
];
