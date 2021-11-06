<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Service\Guard;

interface LimitInterface
{
    public function execute(ProcessContext $processContext): ProcessContext;
}
