<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Enum;

class Frequency
{
    const NONE = 0;
    const HOURLY = 1;
    const DAILY = 2;
    const WEEKLY = 3;
    const MONTHLY = 4;

    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function seconds(): int
    {
        switch ($this->value) {
            case self::NONE:
                return 0;
            case self::HOURLY:
                return 3600;
            case self::DAILY:
                return 86400;
            case self::WEEKLY:
                return 604800;
            case self::MONTHLY:
                return 2592000;
            default:
                throw new \InvalidArgumentException("Invalid frequency value");
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public static function getOptions(): array
    {
        return [
            self::NONE => 'settings.errorlog_option_report_0',
            self::HOURLY => 'settings.errorlog_option_report_1',
            self::DAILY => 'settings.errorlog_option_report_2',
            self::WEEKLY => 'settings.errorlog_option_report_3',
            self::MONTHLY => 'settings.errorlog_option_report_4',
        ];
    }
}
