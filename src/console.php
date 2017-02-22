<?php

require_once __DIR__.'/../vendor/autoload.php';

use App\Command\CalendarUpdateCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new CalendarUpdateCommand);
$application->run();
