<?php

declare(strict_types = 1);

namespace Chapterphp\CloudCronBundle\Service\Guard;

final class ProcessGuard
{
    /**
     * @var LimitInterface[]
     */
    private array $rules;

    private ProcessContext $processContext;

    public function __construct()
    {
        $this->processContext = ProcessContext::create(time());
        $this->rules = [];
    }

    /**
     * @param LimitInterface[] $rules
     */
    public function addRules(array $rules): void
    {
        $this->rules = $rules;
    }

    public function validate(): ProcessContext
    {
        $this->processContext->incrementCounter();

        foreach ($this->rules as $rule) {
            $this->processContext = $rule->execute($this->processContext);

            if ($this->processContext->getError()) {
                return $this->processContext;
            }
        }

        return $this->processContext;
    }
}
