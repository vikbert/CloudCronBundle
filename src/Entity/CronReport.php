<?php

declare(strict_types = 1);

namespace Chapterphp\CloudCronBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cron_report")
 * @ORM\Entity
 */
final class CronReport
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @phpstan-ignore-next-line
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $dueTime;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     * @phpstan-ignore-next-line
     */
    private bool $finished;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @phpstan-ignore-next-line
     */
    private ?float $runDuration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @phpstan-ignore-next-line
     */
    private ?int $exitCode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $output;

    /**
     * @ORM\Column(type="integer")
     * @phpstan-ignore-next-line
     */
    private int $jobId;

    private string $command;

    private DateTimeImmutable $startedAt;

    private function __construct(CronJob $job, DateTimeImmutable $dueTime)
    {
        $this->dueTime = $dueTime;
        $this->jobId = $job->getId();
        $this->command = $job->getCommand();
        $this->startedAt = new DateTimeImmutable();
        $this->finished = false;
        $this->runDuration = 0;
        $this->exitCode = 0;
        $this->output = '';
    }

    public static function start(CronJob $job, DateTimeImmutable $dueTime): self
    {
        $report = new self($job, $dueTime);
        $report->startedAt = new DateTimeImmutable();

        return $report;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDueTime(): DateTimeImmutable
    {
        return $this->dueTime;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function finish(int $exitCode, string $output): void
    {
        $this->finished = true;
        $this->exitCode = $exitCode;
        $this->output = $output;
        $this->runDuration = $this->calculateRunDurationInSeconds();
    }

    public function error(string $output, ?int $exitCode = null): void
    {
        $this->finished = false;
        $this->exitCode = $exitCode;
        $this->output = $output;
        $this->runDuration = $this->calculateRunDurationInSeconds();
    }

    public function getOutput(): string
    {
        return (string) $this->output;
    }

    private function calculateRunDurationInSeconds(): int
    {
        return (new DateTimeImmutable())->getTimestamp() - $this->startedAt->getTimestamp();
    }
}
