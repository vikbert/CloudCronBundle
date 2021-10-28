<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\ValueObject;

use DateTime;
use DateTimeImmutable;

final class DueTimeTolerance
{
    private array $dueTimeCandidates;

    public function __construct(DateTime $targetTime, int $dueTimeToleranceMinutes)
    {
        $referenceTime = $this->resetDateTimeSecondsToZero($targetTime);
        for ($i = 0; $i < $dueTimeToleranceMinutes; ++$i) {
            $this->dueTimeCandidates[] = $referenceTime->modify(sprintf('-%d minutes', $i));
        }
    }

    /**
     * @return DateTimeImmutable[]
     */
    public function getDueTimeCandidates(): array
    {
        return $this->dueTimeCandidates;
    }

    private function resetDateTimeSecondsToZero(DateTime $targetTime): DateTimeImmutable
    {
        $targetTime->setTime((int) $targetTime->format('H'), (int) $targetTime->format('i'), 0);

        return DateTimeImmutable::createFromMutable($targetTime);
    }
}
