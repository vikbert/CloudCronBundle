<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Service;

use Ahc\Cron\Expression;
use DateTime;
use DateTimeImmutable;
use Vikbert\CloudCronBundle\Entity\CronJob;
use Vikbert\CloudCronBundle\Entity\CronReport;
use Vikbert\CloudCronBundle\Repository\CronReportRepository;
use Vikbert\CloudCronBundle\ValueObject\DueTimeTolerance;

final class CronScheduler
{
    private const TOLERANCE_MINUTES = 5;

    private ?DateTimeImmutable $dueTime;
    private CronReportRepository $cronReportRepository;

    public function __construct(CronReportRepository $cronReportRepository)
    {
        $this->cronReportRepository = $cronReportRepository;
    }

    public function schedule(CronJob $job): ?CronReport
    {
        $this->detectDueTimeBackwards($job);

        if ($this->dueTime && !$this->isAlreadyStarted($job)) {
            return CronReport::start($job, $this->dueTime);
        }

        return null;
    }

    private function detectDueTimeBackwards(CronJob $job): void
    {
        $this->dueTime = null;
        $dueTimeTolerance = new DueTimeTolerance(new DateTime(), self::TOLERANCE_MINUTES);

        foreach ($dueTimeTolerance->getDueTimeCandidates() as $candidate) {
            $isDueTimeCandidate = Expression::isDue($job->getSchedule(), $candidate->getTimestamp());
            if ($isDueTimeCandidate) {
                $this->dueTime = $candidate;

                return;
            }
        }
    }

    private function isAlreadyStarted(CronJob $job): bool
    {
        $foundReport = $this->cronReportRepository->countByJobAndDueTime($job, $this->dueTime);

        return $foundReport > 0;
    }
}
