<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks\Dispatchers;

use Witals\Framework\Application;
use PrestoWorld\Contracts\Hooks\Dispatchers\ActionDispatcherInterface;

class SyncDispatcher implements ActionDispatcherInterface
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function dispatch(string $hook, array $hookData, array $args): void
    {
        $this->app->make(\PrestoWorld\Hooks\HookManager::class)
            ->executeDispatchedAction($hookData, $args);
    }
}
