<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Service;

use DateTimeImmutable;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Throwable;
use Vikbert\CloudCronBundle\Entity\CronJob;
use Vikbert\CloudCronBundle\Entity\CronReport;
use Vikbert\CloudCronBundle\Exception\CronBundleException;
use Vikbert\CloudCronBundle\Repository\CronJobRepository;
use Vikbert\CloudCronBundle\Repository\CronReportRepository;

final class CronExecutor
{
    private KernelInterface $kernel;
    private CronJobRepository $cronJobRepository;
    private CronReportRepository $cronReportRepository;
    private CronScheduler $cronScheduler;

    public function __construct(
        KernelInterface $kernel,
        CronScheduler $cronScheduler,
        CronJobRepository $cronJobRepository,
        CronReportRepository $cronReportRepository
    ) {
        $this->kernel = $kernel;
        $this->cronJobRepository = $cronJobRepository;
        $this->cronReportRepository = $cronReportRepository;
        $this->cronScheduler = $cronScheduler;
    }

    public function executeJobs(SymfonyStyle $io): void
    {
        $jobsExecuted = 0;
        $jobs = $this->cronJobRepository->findEnabled();

        foreach ($jobs as $job) {
            $cronReport = $this->cronScheduler->schedule($job);
            $io->comment(sprintf('Try to schedule Job: %s', (string) $job));

            if (null === $cronReport) {
                continue;
            }

            $io->writeln(sprintf('Starting Job "%d": (%s)', $job->getId(), (string) $job));
            $this->execute($job, $cronReport->getDueTime());
            ++$jobsExecuted;
            $io->writeln(sprintf('Finished Job "%d": (%s)', $job->getId(), (string) $job));
        }

        if (0 === $jobsExecuted) {
            $io->warning('No scheduled jobs found for current time');
        }
    }

    private function execute(CronJob $job, DateTimeImmutable $dueTime): void
    {
        $cronReport = CronReport::start($job, $dueTime);
        $this->cronReportRepository->save($cronReport);

        try {
            $process = Process::fromShellCommandline($this->commandWithAbsolutionPath($job));
            $process->start();
            $exitCode = $process->wait();

            if (!$process->isSuccessful()) {
                throw CronBundleException::onFailedSymfonyProcess($process->getErrorOutput());
            }

            $cronReport->finish($exitCode, $process->getOutput());
        } catch (Throwable $e) {
            $cronReport->error($e->getMessage() . ':' . PHP_EOL . $e->getTraceAsString());
        }

        $this->cronReportRepository->save($cronReport);
    }

    private function commandWithAbsolutionPath(CronJob $job): string
    {
        return sprintf(
            '%s/bin/console %s',
            $this->kernel->getProjectDir(),
            $job->getCommand()
        );
    }
}
