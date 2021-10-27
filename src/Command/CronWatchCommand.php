<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Command;

use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use Vikbert\CloudCronBundle\Exception\CronException;
use Vikbert\CloudCronBundle\Service\CronExecutor;

final class CronWatchCommand extends Command
{
    private const ERROR_CODE_LIMIT_EXCEEDED = 999;
    private const ERROR_CODE_EXECUTION_FAILURE = 1;
    private const TIME_LIMIT_IN_SECONDS = 600;
    private const MEMORY_LIMIT_IN_MB = 256;
    private const REPEAT_LIMIT = 60;
    protected static $defaultName = 'cron:watch';
    private $repeatCounter = 0;
    private $startTime;
    private $cronExecutor;

    public function __construct(CronExecutor $cronExecutor)
    {
        parent::__construct();

        $this->cronExecutor = $cronExecutor;
    }

    protected function configure(): void
    {
        $this->setDescription('Watch for cronjobs to be executed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->startTime = time();
        $io = new SymfonyStyle($input, $output);

        while (true) {
            $io->info('Looking for the scheduled cron jobs...');

            try {
                $this->assertMemoryLimit();
                $this->assertTimeLimit();
                $this->assertWatcherRepeatLimit();
            } catch (CronException $e) {
                $io->error($e->getMessage());

                return self::ERROR_CODE_LIMIT_EXCEEDED;
            }

            try {
                $this->cronExecutor->runScheduledJobs($output);
            } catch (Throwable $e) {
                $io->writeln($e->getMessage());
                $io->writeln($e->getTraceAsString());

                return self::ERROR_CODE_EXECUTION_FAILURE;
            }

            $io->comment(sprintf('[%s] Command Watcher will retry after 5 seconds ...', (new DateTime())->format('H:i:s')));
            sleep(5);
            ++$this->repeatCounter;
        }
    }

    private function assertMemoryLimit(): void
    {
        if ((memory_get_usage(true) / 1024 / 1024) > self::MEMORY_LIMIT_IN_MB) {
            throw CronException::onMemoryLimitReached();
        }
    }

    private function assertTimeLimit(): void
    {
        if ((time() - $this->startTime) > self::TIME_LIMIT_IN_SECONDS) {
            throw CronException::onTimeLimitReached();
        }
    }

    private function assertWatcherRepeatLimit(): void
    {
        if ($this->repeatCounter > self::REPEAT_LIMIT) {
            throw CronException::onWatcherRepeatLimitReached();
        }
    }
}
