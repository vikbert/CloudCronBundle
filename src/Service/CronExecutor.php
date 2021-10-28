<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Service;

use DateTimeImmutable;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
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
    private ?array $cachedJobs = null;
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

    /**
     * @return CronJob[]
     */
    private function getEnabledCronJobs(StyleInterface $io): array
    {
        if ($this->cachedJobs === null) {
            $this->cachedJobs = $this->cronJobRepository->findEnabled();
            $io->success(sprintf('Found %d enabled job(s).', count($this->cachedJobs)));
        }

        return $this->cachedJobs;
    }

    public function runScheduledJobs(?OutputInterface $output = null): OutputInterface
    {
        $output = $output ?? new BufferedOutput();

        $io = new SymfonyStyle(new ArrayInput([]), $output);

        $jobs = $this->getEnabledCronJobs($io);
        if (0 === count($jobs)) {
            $io->warning('No enabled jobs found');

            return $output;
        }

        $jobsExecuted = 0;
        foreach ($jobs as $job) {
            $cronReport = $this->cronScheduler->schedule($job);
            $io->comment(sprintf('Try to schedule Job: %s', (string) $job));

            if (null === $cronReport) {
                continue;
            }

            $io->writeln(sprintf('Starting Job "%d": (%s)', $job->getId(), (string) $job));

            $this->execute($job, $cronReport->getDueTime(), $output);

            ++$jobsExecuted;

            $io->writeln(sprintf('Finished Job "%d": (%s)', $job->getId(), (string) $job));
        }

        if (0 === $jobsExecuted) {
            $io->warning('No scheduled jobs found for current time');
        }

        return $output;
    }

    private function execute(CronJob $job, DateTimeImmutable $dueTime, OutputInterface $output): void
    {
        $cronReport = CronReport::start($job, $dueTime);
        $this->cronReportRepository->save($cronReport);

        try {
            $process = Process::fromShellCommandline($this->commandWithAbsolutionPath($job));
            $process->start();
            $exitCode = $process->wait();

            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();
                $output->write($errorOutput);

                throw CronBundleException::onFailedSymfonyProcess($errorOutput);
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
