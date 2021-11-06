<?php

declare(strict_types = 1);

namespace Chapterphp\CloudCronBundle\Service;

use Ahc\Cron\Expression;
use DateTime;
use DateTimeImmutable;
use Chapterphp\CloudCronBundle\Entity\CronJob;
use Chapterphp\CloudCronBundle\Entity\CronReport;
use Chapterphp\CloudCronBundle\Repository\CronReportRepository;
use Chapterphp\CloudCronBundle\ValueObject\DueTimeTolerance;

final class CronScheduler
{
    private const TOLERANCE_MINUTES = 5;

    private ?DateTimeImmutable $dueTime;
    private CronReportRepository $cronReportRepository;

    public function __construct(CronReportRepository $cronReportRepository)
    {
        $this->cronReportRepository = $cronReportRepository;
        $this->dueTime = null;
    }

    public function schedule(CronJob $job): ?CronReport
    {
        $this->detectDueTimeBackwards($job);

        if ($this->dueTime && !$this->isAlreadyStarted($job, $this->dueTime)) {
            return CronReport::start($job, $this->dueTime);
        }

        return null;
    }

    private function detectDueTimeBackwards(CronJob $job): void
    {
        foreach ($this->getDueTimeCandidatesBackwards() as $candidate) {
            if (Expression::isDue($job->getSchedule(), $candidate->getTimestamp())) {
                $this->dueTime = $candidate;

                break;
            }
        }
    }

    private function getDueTimeCandidatesBackwards(): array
    {
        $dueTimeTolerance = new DueTimeTolerance(new DateTime(), self::TOLERANCE_MINUTES);

        return $dueTimeTolerance->getDueTimeCandidates();
    }

    private function isAlreadyStarted(CronJob $job, DateTimeImmutable $dueTime): bool
    {
        return $this->cronReportRepository->foundJobForDueTime($job, $dueTime);
    }
}
