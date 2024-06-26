<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Error extends AbstractEntity
{
    public $uid;
    public string $message = '';
    public int $code = 0;
    public string $file = '';
    public int $line = 0;
    public string $trace = '';
    public string $browserInfo = '';
    public string $serverName = '';
    public string $requestUri = '';
    public int $crdate = 0;
    public int $pageUid = 0;
    public int $rootPageUid = 0;
    public int $workspace = 0;
    public string $IP = '';
    public string $data = '';
    public string $user = '';
    public int $userId = 0;
    public string $channel = '';
    public bool $eventDispatched = false;

    public function __construct(array $errorValues)
    {
        $this->setUid($errorValues['uid']);
        $this->setPageUid($errorValues['page_uid']);
        $this->setMessage($errorValues['message']);
        $this->setCode($errorValues['code']);
        $this->setFile($errorValues['file']);
        $this->setLine($errorValues['line']);
        $this->setTrace($errorValues['trace']);
        $this->setRootPageUid($errorValues['root_page_uid']);
        $this->setBrowserInfo($errorValues['browser_info']);
        $this->setServerName($errorValues['server_name']);
        $this->setRequestUri($errorValues['request_uri']);
        $this->setCrDate($errorValues['crdate']);
        $this->setIP($errorValues['IP'] ?? '');
        $this->setData($errorValues['data'] ?? '');
        $this->setUser($errorValues['user'] ?? '');
        $this->setUserId($errorValues['user_id'] ?? 0);
        $this->setWorkspace($errorValues['workspace'] ?? 0);
        $this->setEventDispatched($errorValues['event_dispatched'] ?? true);
        $this->setChannel($errorValues['channel'] ?? '');
    }

    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setTrace(string $trace): void
    {
        $this->trace = $trace;
    }

    public function getTrace(): string
    {
        return $this->trace;
    }

    public function setBrowserInfo(string $browserInfo): void
    {
        $this->browserInfo = $browserInfo;
    }

    public function getBrowserInfo(): string
    {
        return $this->browserInfo;
    }

    public function setServerName(string $serverName): void
    {
        $this->serverName = $serverName;
    }

    public function getServerName(): string
    {
        return $this->serverName;
    }

    public function setRequestUri(string $requestUri): void
    {
        $this->requestUri = $requestUri;
    }

    public function setCrdate(int $crdate): void
    {
        $this->crdate = $crdate;
    }

    public function getCrdate(): int
    {
        return $this->crdate;
    }

    public function setPageUid(int $pageUid): void
    {
        $this->pageUid = $pageUid;
    }

    public function getPageUid(): int
    {
        return $this->pageUid;
    }

    public function setRootPageUid(int $rootPageUid): void
    {
        $this->rootPageUid = $rootPageUid;
    }

    public function getRootPageUid(): int
    {
        return $this->rootPageUid;
    }

    public function setWorkspace(int $workspace): void
    {
        $this->workspace = $workspace;
    }

    public function getWorkspace(): int
    {
        return $this->workspace;
    }

    public function setIP(string $IP): void
    {
        $this->IP = $IP;
    }

    public function getIP(): string
    {
        return $this->IP;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setEventDispatched($eventDispatched): void
    {
        $this->eventDispatched = (bool) $eventDispatched;
    }

    public function getEventDispatched(): bool
    {
        return $this->eventDispatched;
    }

    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }
}
