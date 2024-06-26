<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Settings extends AbstractEntity
{
    protected bool $generalEnable = false;
    protected int $generalExpireDays = 0;
    protected bool $slackEnable = false;
    protected string $slackAuthToken = '';
    protected string $slackChannelId = '';
    protected string $slackReportType = '';
    protected int $slackOccurrenceType = 0;
    protected bool $openaiEnable = false;
    protected string $openaiAuthToken = '';
    protected string $openaiModel = '';
    protected string $prePrompt = '';

    public function setGeneralEnable(bool $generalEnable = false): void
    {
        $this->generalEnable = $generalEnable;
    }

    public function getGeneralEnable(): bool
    {
        return $this->generalEnable;
    }

    public function setGeneralExpireDays(int $generalExpireDays): void
    {
        $this->generalExpireDays = $generalExpireDays;
    }

    public function getGeneralExpireDays(): int
    {
        return $this->generalExpireDays;
    }

    public function setSlackEnable(bool $slackEnable = false): void
    {
        $this->slackEnable = $slackEnable;
    }

    public function getSlackEnable(): bool
    {
        return $this->slackEnable;
    }

    public function setSlackAuthToken(string $slackAuthToken): void
    {
        $this->slackAuthToken = $slackAuthToken;
    }

    public function getSlackAuthToken(): string
    {
        return $this->slackAuthToken;
    }

    public function setSlackChannelId(string $slackChannelId): void
    {
        $this->slackChannelId = $slackChannelId;
    }

    public function getSlackChannelId(): string
    {
        return $this->slackChannelId;
    }

    public function setSlackReportType(string $slackReportType): void
    {
        $this->slackReportType = $slackReportType;
    }

    public function getSlackReportType(): string
    {
        return $this->slackReportType;
    }

    public function setSlackOccurrenceType(int $slackOccurrenceType): void
    {
        $this->slackOccurrenceType = $slackOccurrenceType;
    }

    public function getSlackOccurrenceType(): int
    {
        return $this->slackOccurrenceType;
    }

    public function setOpenaiEnable(bool $openaiEnable = false): void
    {
        $this->openaiEnable = $openaiEnable;
    }

    public function getOpenaiEnable(): bool
    {
        return $this->openaiEnable;
    }

    public function setOpenaiAuthToken(string $openaiAuthToken): void
    {
        $this->openaiAuthToken = $openaiAuthToken;
    }

    public function getOpenaiAuthToken(): string
    {
        return $this->openaiAuthToken;
    }

    public function setOpenaiModel(string $openaiModel): void
    {
        $this->openaiModel = $openaiModel;
    }

    public function getOpenaiModel(): string
    {
        return $this->openaiModel;
    }

    public function setPrePrompt(string $prePrompt): void
    {
        $this->prePrompt = $prePrompt;
    }

    public function getPrePrompt(): string
    {
        return $this->prePrompt;
    }
}
