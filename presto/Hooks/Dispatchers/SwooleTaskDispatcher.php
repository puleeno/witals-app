<?php

declare(strict_types=1);

namespace PrestoWorld\Hooks\Dispatchers;

use Witals\Framework\Application;
use PrestoWorld\Contracts\Hooks\Dispatchers\ActionDispatcherInterface;
use Swoole\Http\Server as SwooleHttpServer;
use Swoole\Server as SwooleServer;

class SwooleTaskDispatcher implements ActionDispatcherInterface
{
    protected Application $app;
    protected $server;

    public function __construct(Application $app, $server)
    {
        $this->app = $app;
        $this->server = $server;
    }

    public function dispatch(string $hook, array $hookData, array $args): void
    {
        if ($this->server instanceof SwooleHttpServer || $this->server instanceof SwooleServer) {
            // Push to Task Worker
            $this->server->task([
                'type' => 'hook_action',
                'hook' => $hook,
                'hook_data' => $hookData,
                'args' => $args
            ]);
            return;
        }

        // Fallback to sync if server is not capable
        $this->app->make(\PrestoWorld\Hooks\HookManager::class)
            ->executeDispatchedAction($hookData, $args);
    }
}
