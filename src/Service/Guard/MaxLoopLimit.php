<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Service\Guard;

final class MaxLoopLimit implements LimitInterface
{
    public const MAX_LOOP_LIMIT = 60;
    private int $maxLoopLimit;

    public function __construct(?int $maxLoopLimit = null)
    {
        $this->maxLoopLimit = $maxLoopLimit ?? self::MAX_LOOP_LIMIT;
    }

    public function execute(ProcessContext $processContext): ProcessContext
    {
        if ($processContext->loopCounter() > $this->maxLoopLimit) {
            $processContext->setError('CRON_WATCHER: MAX loop limit reached! Stop cron watch command.');
        }

        return $processContext;
    }
}
