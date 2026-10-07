<?php

declare(strict_types=1);

namespace App\Foundation\Database;

use Psr\Log\LoggerTrait;
use Psr\Log\LoggerInterface;
use App\Foundation\Debug\DebugBar;

/**
 * Database Query Interceptor
 * 
 * Intercepts database queries for profiling and statistics
 * when the Debug Bar is enabled.
 */
class QueryInterceptor implements LoggerInterface
{
    use LoggerTrait;

    protected DebugBar $debugBar;

    public function __construct(DebugBar $debugBar)
    {
        $this->debugBar = $debugBar;
    }

    /**
     * Log a message to the interceptor
     */
    public function log($level, $message, array $context = []): void
    {
        // Cycle DBAL logs the actual SQL query as an 'info' message
        // Filter out transaction commands and other non-query logs
        if ($level === 'info' && $this->isSqlQuery((string)$message)) {
            $this->captureQuery((string)$message, $context);
        }
    }

    /**
     * Determine if the message is a valid SQL query to profile
     */
    protected function isSqlQuery(string $message): bool
    {
        $message = strtolower($message);
        $ignored = ['begin', 'commit', 'rollback', 'savepoint', 'release savepoint'];
        
        foreach ($ignored as $term) {
            if (str_starts_with($message, $term)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Capture query details and send to DebugBar
     */
    protected function captureQuery(string $sql, array $context): void
    {
        $elapsed = $context['elapsed'] ?? 0.0;
        
        // Convert to seconds if it looks like milliseconds (Cycle varies)
        // Usually Cycle logs 'elapsed' in seconds (float)
        
        $this->debugBar->logQuery($sql, (float)$elapsed, $context);
    }
}
