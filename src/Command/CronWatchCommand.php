<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use Vikbert\CloudCronBundle\Service\CronExecutor;
use Vikbert\CloudCronBundle\Service\Guard\MaxLoopLimit;
use Vikbert\CloudCronBundle\Service\Guard\MaxMemoryLimit;
use Vikbert\CloudCronBundle\Service\Guard\MaxTimeLimit;
use Vikbert\CloudCronBundle\Service\Guard\ProcessGuard;

final class CronWatchCommand extends Command
{
    private const ERROR_CODE_LIMIT_EXCEEDED = 999;
    private const ERROR_CODE_EXECUTION_FAILURE = 1;

    private CronExecutor $cronExecutor;

    protected static $defaultName = 'cron:watch';

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
        $io = new SymfonyStyle($input, $output);
        $processGuard = $this->initProcessGuard();

        while (true) {
            $processContext = $processGuard->validate();
            if ($processContext->hasError()) {
                $io->error($processContext->getError());

                return self::ERROR_CODE_LIMIT_EXCEEDED;
            }

            try {
                $this->cronExecutor->executeJobs($io);
                sleep(5);
            } catch (Throwable $e) {
                $io->writeln($e->getTraceAsString());

                return self::ERROR_CODE_EXECUTION_FAILURE;
            }
        }
    }

    private function initProcessGuard(): ProcessGuard
    {
        $processGuard = new ProcessGuard();
        $processGuard->addRules([
            new MaxMemoryLimit(),
            new MaxLoopLimit(),
            new MaxTimeLimit(),
        ]);

        return $processGuard;
    }
}
