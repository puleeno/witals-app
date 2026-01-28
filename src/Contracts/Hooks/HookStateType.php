<?php

declare(strict_types=1);

namespace Witals\Framework\Contracts\Hooks;

/**
 * Hook State Type Enum
 * Defines how hook instances are managed in memory.
 */
enum HookStateType: string
{
    /**
     * Volatile: Fresh instance per execution, immediate cleanup.
     * Best for: Actions that don't need state persistence.
     */
    case VOLATILE = 'volatile';

    /**
     * Scoped: Instance pooled within the same request/flow.
     * Best for: Filters called multiple times in one request.
     */
    case SCOPED = 'scoped';

    /**
     * Shared: State persisted across requests/processes via shared memory.
     * Best for: Counters, caches, or data that must survive across flows.
     * Requires: APCu or Swoole Table.
     */
    case SHARED = 'shared';
}
