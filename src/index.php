<?php

require_once __DIR__.'/../vendor/autoload.php';

use App\Task\Calendar;

$container = require_once __DIR__.'/bootstrap.php';

/** @var Calendar $calendar */
$calendar = $container->get(Calendar::class);
$calendar->run(TARGET_URL, GOOGLE_CALENDAR_ID, GOOGLE_CALENDAR_CREATOR);
