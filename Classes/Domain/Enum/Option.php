<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Enum;

class Option
{
    const NONE = 0;
    const FIRST = 1;
    const EACH = 2;

    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public static function getOptions(): array
    {
        return [
            self::NONE => 'settings.errorlog_option_occurence_0',
            self::FIRST => 'settings.errorlog_option_occurence_1',
            self::EACH => 'settings.errorlog_option_occurence_2',
        ];
    }
}
