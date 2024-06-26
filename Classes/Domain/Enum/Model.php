<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Enum;

class Model
{
    const NONE = '';
    const GPT35 = 'gpt-3.5-turbo';
    const GPT4O = 'gpt-4o';
    const GPT4T = 'gpt-4-turbo';

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function getOptions(): array
    {
        return [
            self::NONE => '---',
            self::GPT35 => 'settings.model.openai35t',
            self::GPT4O => 'settings.model.openai4o',
            self::GPT4T => 'settings.model.openai4t',
        ];
    }
}
