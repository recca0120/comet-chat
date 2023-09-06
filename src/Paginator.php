<?php

namespace Recca0120\CometChat;

use Countable;
use Iterator;
use ReturnTypeWillChange;

class Paginator implements Iterator, Countable
{
    private array $items;
    private array $meta;

    public function __construct(array $result)
    {
        $this->items = $result['data'];
        $this->meta = $result['meta'];
    }

    public function hasMorePage(): bool
    {
        if (! isset($this->meta['pagination'])) {
            return (int) $this->meta['current']['count'] !== 0;
        }

        return $this->meta['pagination']['current_page'] < $this->meta['pagination']['total_pages'];
    }

    public function nextQuery(): array
    {
        if (! isset($this->meta['pagination'])) {
            return [
                'id' => $this->meta['next']['id'],
                'sentAt' => $this->meta['next']['sentAt'],
                'affix' => $this->meta['next']['affix'],
            ];
        }

        return ['page' => $this->meta['pagination']['current_page'] + 1];
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function current(): mixed
    {
        return current($this->items);
    }

    #[ReturnTypeWillChange]
    public function next(): mixed
    {
        return next($this->items);
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
}
