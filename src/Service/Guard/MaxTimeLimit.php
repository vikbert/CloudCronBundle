<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Service\Guard;

final class MaxTimeLimit implements LimitInterface
{
    public const MAX_TIME_LIMIT_IN_SECONDS = 600; // 10 minutes

    public function execute(ProcessContext $processContext): ProcessContext
    {
        if (time() - $processContext->getstartTime() > self::MAX_TIME_LIMIT_IN_SECONDS) {
            $processContext->setError('CRON_WATCHER: MAX watch time limit reached! Stop cron watch command.');
        }

        return $processContext;
    }
}
