<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Model;

use DateTime;

class Filter
{
    protected string $start = '';
    protected string $end = '';
    private string $search = '';
    protected int $rootPage = 0;
    protected int $limit = 25;
    protected bool $eventDispatched = true;

    /**
     * @return string
     */
    public function getStart(): string
    {
        return $this->start;
    }

    /**
     * @param string $start
     * @return void
     */
    public function setStart(string $start): void
    {
        $this->start = $start;
    }

    /**
     * @return string
     */
    public function getEnd(): string
    {
        return $this->end;
    }

    /**
     * @param string $end
     * @return void
     */
    public function setEnd(string $end): void
    {
        $this->end = $end;
    }

    /**
     * @return string
     */
    public function getSearch(): string
    {
        return $this->search;
    }

    /**
     * @param string $search
     * @return void
     */
    public function setSearch(string $search): void
    {
        $this->search = $search;
    }

    /**
     * @return int
     */
    public function getRootPage(): int
    {
        return $this->rootPage;
    }

    /**
     * @param int $rootPage
     * @return void
     */
    public function setRootPage(int $rootPage): void
    {
        $this->rootPage = $rootPage;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return void
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getEndTimestamp(): int
    {
        return $this->getTimeStampFromString($this->end);
    }

    public function getStartTimestamp(): int
    {
        return $this->getTimeStampFromString($this->start);
    }

    private function getTimeStampFromString(string $data): int
    {
        if ($data === '') {
            return 0;
        }

        $date = new DateTime($data);
        return $date->getTimestamp();
    }

    public function setEventDispatched(bool $eventDispatched): void
    {
        $this->eventDispatched = $eventDispatched;
    }

    public function getEventDispatched(): bool
    {
        return $this->eventDispatched;
    }
}
