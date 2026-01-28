<?php

declare(strict_types=1);

namespace App\Foundation\Debug;

class DebugBar
{
    protected float $startTime;
    protected array $queries = [];
    protected array $benchmarks = [];

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->startTime = microtime(true);
        $this->queries = [];
        $this->benchmarks = [];
    }

    public function startTimer(string $name): void
    {
        $this->benchmarks[$name] = microtime(true);
    }

    public function endTimer(string $name): float
    {
        if (!isset($this->benchmarks[$name])) {
            return 0;
        }
        $duration = microtime(true) - $this->benchmarks[$name];
        $this->benchmarks[$name] = $duration;
        return $duration;
    }

    public function logQuery(string $sql, float $time, array $bindings = []): void
    {
        $this->queries[] = [
            'sql' => $sql,
            'time' => $time,
            'bindings' => $bindings
        ];
    }

    public function render(): string
    {
        $totalTime = (microtime(true) - $this->startTime) * 1000;
        $memory = memory_get_peak_usage(true) / 1024 / 1024;
        $queryCount = count($this->queries);
        $queryTime = array_sum(array_column($this->queries, 'time')) * 1000;

        $html = "
        <!-- PrestoWorld Debug Bar -->
        <style>
            #pw-debug-bar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 35px;
                background: #1e293b;
                color: #f8fafc;
                font-family: 'Inter', system-ui, sans-serif;
                font-size: 12px;
                display: flex;
                align-items: center;
                padding: 0 20px;
                border-top: 1px solid #334155;
                z-index: 999999;
                box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
            }
            .pw-db-item {
                display: flex;
                align-items: center;
                margin-right: 25px;
            }
            .pw-db-label { color: #94a3b8; margin-right: 6px; }
            .pw-db-value { font-weight: 600; color: #818cf8; }
            .pw-db-icon { margin-right: 6px; opacity: 0.7; }
        </style>
        <div id='pw-debug-bar'>
            <div class='pw-db-item'>
                <span class='pw-db-icon'>⚡</span>
                <span class='pw-db-label'>Time:</span>
                <span class='pw-db-value'>" . number_format($totalTime, 2) . "ms</span>
            </div>
            <div class='pw-db-item'>
                <span class='pw-db-icon'>🧠</span>
                <span class='pw-db-label'>Memory:</span>
                <span class='pw-db-value'>" . number_format($memory, 2) . "MB</span>
            </div>
            <div class='pw-db-item'>
                <span class='pw-db-icon'>🗄️</span>
                <span class='pw-db-label'>Queries:</span>
                <span class='pw-db-value'>$queryCount (" . number_format($queryTime, 2) . "ms)</span>
            </div>
            <div class='pw-db-item' style='margin-left: auto;'>
                <span class='pw-db-label'>PrestoWorld Debug</span>
            </div>
        </div>
        ";

        return $html;
    }
}
