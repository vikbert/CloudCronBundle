<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Exception;

use Exception;

final class CronBundleException extends Exception
{
    public static function onInvalidCronCommand(string $command): self
    {
        return new self(sprintf('Forbidden characters found in job command: %s.', $command));
    }

    public static function onFailedSymfonyProcess(string $errorOutput): self
    {
        return new self(sprintf('[Symfony Process error] %s', $errorOutput));
    }
}
