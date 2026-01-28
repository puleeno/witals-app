<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks\Registries;

use Swoole\Table;
use PrestoWorld\Contracts\Hooks\Registries\HookRegistryInterface;
use PrestoWorld\Contracts\Hooks\HookStateType;

class SwooleTableRegistry implements HookRegistryInterface
{
    protected Table $table;

    public function __construct(int $size = 2048)
    {
        $this->table = new Table($size);
        $this->table->column('hook_name', Table::TYPE_STRING, 64);
        $this->table->column('callback', Table::TYPE_STRING, 8192); // Increased to 8KB for Closures
        $this->table->column('priority', Table::TYPE_INT, 4);
        $this->table->column('type', Table::TYPE_STRING, 10);
        $this->table->column('state_type', Table::TYPE_STRING, 10);
        $this->table->create();
    }

    public function set(string $type, string $hook, string $callback, int $priority, HookStateType $stateType = HookStateType::VOLATILE): void
    {
        $key = md5($type . $hook . $callback . $priority);
        
        $this->table->set($key, [
            'hook_name'  => $hook,
            'callback'   => $callback,
            'priority'   => $priority,
            'type'       => $type,
            'state_type' => $stateType->value
        ]);
    }

    public function get(string $type, string $hook): array
    {
        $hooks = [];
        foreach ($this->table as $row) {
            if ($row['type'] === $type && $row['hook_name'] === $hook) {
                $hooks[] = [
                    'callback'   => $row['callback'],
                    'priority'   => $row['priority'],
                    'state_type' => $row['state_type']
                ];
            }
        }

        usort($hooks, fn($a, $b) => $a['priority'] <=> $b['priority']);
        return $hooks;
    }

    public function remove(string $type, string $hook, string $callback, int $priority): void
    {
        $key = md5($type . $hook . $callback . $priority);
        $this->table->del($key);
    }

    public function clear(string $type, string $hook, ?int $priority = null): void
    {
        foreach ($this->table as $key => $row) {
            if ($row['type'] === $type && $row['hook_name'] === $hook) {
                if ($priority === null || $row['priority'] === $priority) {
                    $this->table->del($key);
                }
            }
        }
    }
}
