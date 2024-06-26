<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Event;

use RD\ErrorLog\Domain\Enum\Frequency;

class ReportEvent
{
    private array $errors;
    private Frequency $frequency;

    public function __construct(Frequency $frequency, array $errors = [])
    {
        $this->frequency = $frequency;
        $this->errors = $errors;
    }

    public function getFrequency(): Frequency
    {
        return $this->frequency;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
