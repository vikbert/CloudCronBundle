<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Service\Guard;

final class MaxMemoryLimit implements LimitInterface
{
    private const MEMORY_LIMIT_IN_MB = 256;
    private ?int $memoryLimitInMB;

    public function __construct(?int $memoryLimitInMB = null)
    {
        $this->memoryLimitInMB = $memoryLimitInMB ?? self::MEMORY_LIMIT_IN_MB;
        var_dump($this->memoryLimitInMB);
    }

    public function execute(ProcessContext $processContext): ProcessContext
    {
        if (memory_get_usage(true) / 1024 / 1024 > $this->memoryLimitInMB) {
            $processContext->setError('CRON_WATCHER: Memory limit reached! Stop cron watch command.');
        }

        return $processContext;
    }
}
