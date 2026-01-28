<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks\State;

use PrestoWorld\Contracts\Hooks\StateDriverInterface;
use Swoole\Table;
use Swoole\Lock;

/**
 * Swoole State Driver
 * High-performance shared memory state for long-running processes.
 */
class SwooleStateDriver implements StateDriverInterface
{
    protected Table $table;
    protected array $locks = [];

    public function __construct()
    {
        $this->table = new Table(1024);
        $this->table->column('data', Table::TYPE_STRING, 4096); // 4KB for state
        $this->table->create();
    }

    public function get(string $key): array
    {
        $row = $this->table->get($key);
        return $row ? unserialize($row['data']) : [];
    }

    public function set(string $key, array $data): void
    {
        $this->table->set($key, ['data' => serialize($data)]);
    }

    public function lock(string $key): bool
    {
        if (!isset($this->locks[$key])) {
            $this->locks[$key] = new Lock(SWOOLE_MUTEX);
        }
        return $this->locks[$key]->lock();
    }

    public function unlock(string $key): void
    {
        if (isset($this->locks[$key])) {
            $this->locks[$key]->unlock();
        }
    }
}
