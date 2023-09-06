<?php

namespace Recca0120\CometChat;

use Countable;
use Iterator;

class Paginator implements Iterator, Countable
{
    private array $items;
    private array $meta;

    public function __construct(array $result)
    {
        $this->items = $result['data'];
        $this->meta = $result['meta'];
    }

    public function hasMorePages(): bool
    {
        if ($this->hasPagination()) {
            return $this->meta['pagination']['current_page'] < $this->meta['pagination']['total_pages'];
        }

        return (int) $this->meta['current']['count'] !== 0;
    }

    public function nextQuery(): array
    {
        if ($this->hasPagination()) {
            return ['page' => ++$this->meta['pagination']['current_page']];
        }

        return $this->meta['next'];
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function current(): mixed
    {
        return current($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function key(): string|int|null
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    private function hasPagination(): bool
    {
        return array_key_exists('pagination', $this->meta);
    }
}
