<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Service\Guard;

final class MaxLoopLimit implements LimitInterface
{
    public const MAX_LOOP_LIMIT = 60;

    public function execute(ProcessContext $processContext): ProcessContext
    {
        if ($processContext->loopCounter() > self::MAX_LOOP_LIMIT) {
            $processContext->setError('CRON_WATCHER: MAX loop limit reached! Stop cron watch command.');
        }

        return $processContext;
    }
}
