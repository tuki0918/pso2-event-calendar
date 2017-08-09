<?php

namespace App\Command;

use App\Task\Calendar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CalendarUpdateCommand extends Command
{
    protected function configure()
    {
        $this->setName('calendar:fetch')
            ->setDescription('Fetch the current event calendar.')
            ->addOption(
                'deploy',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will deploy to google calendar.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // コンテナ読み込み後、定数利用可能
        $container = require_once __DIR__.'/../bootstrap.php';

        /** @var Calendar $calendar */
        $calendar = $container->get(Calendar::class);

        $output->writeln('[Campaign:fetch] start');

        // キャンペーンの取得
        $campaigns = $calendar->get(TARGET_URL);

        $output->writeln('[Campaign:fetch] end');

        if ($this->isDeploy($input)) {
            $output->writeln('[Campaign:deploy] start');
            $calendar->deploy(GOOGLE_CALENDAR_ID, GOOGLE_CALENDAR_CREATOR, ...$campaigns);
            $output->writeln('[Campaign:deploy] end');
        }
    }

    /**
     * @param InputInterface $input
     * @return bool
     */
    private function isDeploy(InputInterface $input): bool
    {
        return $input->getOption('deploy');
    }
}
