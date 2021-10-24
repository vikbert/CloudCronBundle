<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cron_report")
 * @ORM\Entity
 */
class CronReport
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $dueTime;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private bool $finished;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $runDuration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $exitCode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $output;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $jobId;

    private string $command;

    private DateTimeImmutable $startedAt;

    private function __construct(CronJob $job, DateTimeImmutable $dueTime)
    {
        $this->dueTime = $dueTime;
        $this->jobId = $job->getId();
        $this->command = $job->getCommand();
        $this->startedAt = new DateTimeImmutable();
        $this->finished = false;
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

    public function getOutput(): ?string
    {
        return $this->output;
    }

    private function calculateRunDurationInSeconds(): int
    {
        return (new DateTimeImmutable())->getTimestamp() - $this->startedAt->getTimestamp();
    }
}
