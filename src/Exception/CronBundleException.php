<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Exception;

use Exception;

final class CronBundleException extends Exception
{
    public static function onMemoryLimitReached(): self
    {
        return new self('CRON_WATCHER: Memory limit reached! Stop cron watch command.');
    }

    public static function onTimeLimitReached(): self
    {
        return new self('CRON_WATCHER: Watch time limit reached! Stop cron watch command.');
    }

    public static function onWatcherRepeatLimitReached(): self
    {
        return new self('CRON_WATCHER: Watch loop limit reached! Stop cron watch command.');
    }

    public static function onInvalidCronCommand(string $command): self
    {
        return new self(sprintf('Forbidden characters found in job command: %s.', $command));
    }

    public static function onFailedSymfonyProcess(string $errorOutput): self
    {
        return new self(sprintf('[Symfony Process error] %s', $errorOutput));
    }
}
