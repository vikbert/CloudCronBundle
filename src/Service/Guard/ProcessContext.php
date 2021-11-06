<?php

declare(strict_types = 1);

namespace Chapterphp\CloudCronBundle\Service\Guard;

final class ProcessContext
{
    private int $startTime;
    private int $loopCounter;
    private ?string $error;

    private function __construct(int $startTime)
    {
        $this->startTime = $startTime;
        $this->loopCounter = 0;
        $this->error = null;
    }

    public static function create(int $startTime): self
    {
        return new self($startTime);
    }

    public function incrementCounter(): void
    {
        ++$this->loopCounter;
    }

    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function loopCounter(): int
    {
        return $this->loopCounter;
    }

    public function hasError(): bool
    {
        return null !== $this->error;
    }

    public function getError(): string
    {
        return (string) $this->error;
    }
}
