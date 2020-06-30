<?php

namespace AtlassianConnectLaravel\Api;

class PaginationInfo
{
    protected string $offsetKey;
    protected string $limitKey;
    protected string $totalKey;
    protected string $resultsKey;
    protected int $limit = 50;
    protected int $offset = 0;

    public function __construct(string $offsetKey, string $limitKey, string $totalKey, string $resultsKey)
    {
        $this->offsetKey = $offsetKey;
        $this->limitKey = $limitKey;
        $this->totalKey = $totalKey;
        $this->resultsKey = $resultsKey;
    }

    public function getOffsetKey(): string
    {
        return $this->offsetKey;
    }

    public function getLimitKey(): string
    {
        return $this->limitKey;
    }

    public function getTotalKey(): string
    {
        return $this->totalKey;
    }

    public function getResultsKey(): string
    {
        return $this->resultsKey;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset)
    {
        $this->offset = $offset;

        return $this;
    }
}
