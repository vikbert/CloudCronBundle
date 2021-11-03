<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Service\Guard;

final class MaxMemoryLimit implements LimitInterface
{
    private const MEMORY_LIMIT_IN_MB = 256;

    public function execute(ProcessContext $processContext): ProcessContext
    {
        if (memory_get_usage(true) / 1024 / 1024 > self::MEMORY_LIMIT_IN_MB) {
            $processContext->setError('CRON_WATCHER: Memory limit reached! Stop cron watch command.');
        }

        return $processContext;
    }
}
