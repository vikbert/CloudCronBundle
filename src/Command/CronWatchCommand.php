<?php

declare(strict_types = 1);

namespace Chapterphp\CloudCronBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Throwable;
use Chapterphp\CloudCronBundle\Service\CronExecutor;
use Chapterphp\CloudCronBundle\Service\Guard\MaxLoopLimit;
use Chapterphp\CloudCronBundle\Service\Guard\MaxMemoryLimit;
use Chapterphp\CloudCronBundle\Service\Guard\MaxTimeLimit;
use Chapterphp\CloudCronBundle\Service\Guard\ProcessGuard;

final class CronWatchCommand extends Command
{
    private const ERROR_CODE_LIMIT_EXCEEDED = 999;
    private const ERROR_CODE_EXECUTION_FAILURE = 1;

    private CronExecutor $cronExecutor;
    private ContainerInterface $container;

    protected static $defaultName = 'cron:watch';

    public function __construct(CronExecutor $cronExecutor, ContainerInterface $container)
    {
        parent::__construct();

        $this->cronExecutor = $cronExecutor;
        $this->container = $container;
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
                sleep(60);
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
            new MaxMemoryLimit($this->container->getParameter('cron_watcher.max_memory_limit')),
            new MaxLoopLimit($this->container->getParameter('cron_watcher.max_loop_limit')),
            new MaxTimeLimit($this->container->getParameter('cron_watcher.max_time_limit')),
        ]);

        return $processGuard;
    }
}
