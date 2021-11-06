<?php

declare(strict_types = 1);

namespace Chapterphp\CloudCronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Chapterphp\CloudCronBundle\Exception\CronBundleException;

/**
 * @ORM\Table(name="cron_job")
 * @ORM\Entity
 */
final class CronJob
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @phpstan-ignore-next-line
     */
    private int $id;

    /**
     * @ORM\Column(length=1024, unique=true)
     */
    private string $command;

    /**
     * @ORM\Column(length=128)
     */
    private string $schedule;

    /**
     * @ORM\Column(length=128, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private bool $enabled;

    public function __construct(string $command, string $schedule, bool $enabled, ?string $description = null)
    {
        $this->command = trim($command);
        $this->schedule = trim($schedule);
        $this->enabled = $enabled;
        $this->description = $description;
    }

    public function __toString(): string
    {
        return $this->command;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCommand(): string
    {
        $this->validateCommandCharacters();

        return $this->command;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    private function validateCommandCharacters(): void
    {
        $forbiddenChars = ['&', '|', ';'];

        $cleanCommand = str_replace($forbiddenChars, '', $this->command);

        if ($cleanCommand !== $this->command) {
            throw CronBundleException::onInvalidCronCommand($this->command);
        }
    }
}
