<?php

declare(strict_types=1);

namespace App\Http;

use Witals\Framework\Http\Request;
use Witals\Framework\Http\Response;

/**
 * State Demo Trait for Kernel
 * Demonstrates state management capabilities
 */
trait KernelStateDemoTrait
{
    /**
     * Handle state demo route
     */
    protected function handleStateDemo(Request $request): Response
    {
        $state = $this->app->state();

        // Increment request counter
        $requestCount = $state->get('request_count', 0);
        $requestCount++;
        $state->set('request_count', $requestCount);

        // For stateful manager, also track persistent counter
        $persistentCount = 0;
        if ($state->isStateful()) {
            $persistentCount = $state->getPersistent('total_requests', 0);
            $persistentCount++;
            $state->setPersistent('total_requests', $persistentCount);
        }

        // Get stats
        $stats = method_exists($state, 'getStats') ? $state->getStats() : [];

        $data = [
            'state_type' => $state->isStateful() ? 'Stateful (RoadRunner)' : 'Stateless (Traditional)',
            'is_stateful' => $state->isStateful(),
            'current_request' => [
                'request_count' => $requestCount,
                'explanation' => 'This counter is request-scoped and will be 1 on each request in stateless mode',
            ],
        ];

        if ($state->isStateful()) {
            $data['persistent_state'] = [
                'total_requests' => $persistentCount,
                'explanation' => 'This counter persists across requests in RoadRunner mode',
            ];
            $data['stats'] = $stats;
            $data['note'] = 'Refresh this page multiple times to see the persistent counter increase!';
        } else {
            $data['note'] = 'In stateless mode, state is cleared after each request. Switch to RoadRunner to see persistent state.';
        }

        // Return JSON for API or HTML for browser
        if ($request->header('Accept') === 'application/json') {
            return Response::json($data);
        }

        return $this->renderStateDemoHtml($data);
    }

    /**
     * Render state demo HTML
     */
    protected function renderStateDemoHtml(array $data): Response
    {
        $isStateful = $data['is_stateful'];
        $stateType = $data['state_type'];
        $requestCount = $data['current_request']['request_count'];
        $persistentCount = $data['persistent_state']['total_requests'] ?? 0;
        $note = $data['note'];

        $statsHtml = '';
        if ($isStateful && isset($data['stats'])) {
            $stats = $data['stats'];
            $statsHtml = <<<HTML
            <div class="stats">
                <h3>📊 State Statistics</h3>
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-label">Request State Items</div>
                        <div class="stat-value">{$stats['request_state_count']}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Persistent State Items</div>
                        <div class="stat-value">{$stats['persistent_state_count']}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Request Memory</div>
                        <div class="stat-value">{$this->formatBytes($stats['request_memory'])}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Persistent Memory</div>
                        <div class="stat-value">{$this->formatBytes($stats['persistent_memory'])}</div>
                    </div>
                </div>
            </div>
            HTML;
        }

        $persistentHtml = '';
        if ($isStateful) {
            $persistentHtml = <<<HTML
            <div class="counter persistent">
                <div class="counter-label">🔄 Persistent Counter</div>
                <div class="counter-value">{$persistentCount}</div>
                <div class="counter-desc">Survives across requests</div>
            </div>
            HTML;
        }

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>State Management Demo - Witals Framework</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    padding: 2rem;
                    color: white;
                }
                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                }
                h1 {
                    font-size: 2.5rem;
                    margin-bottom: 1rem;
                    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
                }
                .badge {
                    display: inline-block;
                    padding: 0.5rem 1rem;
                    background: rgba(255,255,255,0.2);
                    border-radius: 20px;
                    margin-bottom: 2rem;
                    backdrop-filter: blur(10px);
                }
                .counters {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 1.5rem;
                    margin: 2rem 0;
                }
                .counter {
                    background: rgba(255,255,255,0.1);
                    padding: 2rem;
                    border-radius: 15px;
                    backdrop-filter: blur(10px);
                    text-align: center;
                    border: 2px solid rgba(255,255,255,0.2);
                }
                .counter.persistent {
                    background: rgba(76, 175, 80, 0.2);
                    border-color: rgba(76, 175, 80, 0.5);
                }
                .counter-label {
                    font-size: 1.1rem;
                    margin-bottom: 1rem;
                    opacity: 0.9;
                }
                .counter-value {
                    font-size: 3rem;
                    font-weight: bold;
                    margin: 1rem 0;
                }
                .counter-desc {
                    font-size: 0.9rem;
                    opacity: 0.8;
                }
                .note {
                    background: rgba(255,255,255,0.15);
                    padding: 1.5rem;
                    border-radius: 10px;
                    margin: 2rem 0;
                    border-left: 4px solid rgba(255,255,255,0.5);
                }
                .stats {
                    background: rgba(0,0,0,0.2);
                    padding: 2rem;
                    border-radius: 15px;
                    margin: 2rem 0;
                }
                .stats h3 {
                    margin-bottom: 1.5rem;
                }
                .stat-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 1rem;
                }
                .stat-item {
                    background: rgba(255,255,255,0.1);
                    padding: 1rem;
                    border-radius: 8px;
                }
                .stat-label {
                    font-size: 0.9rem;
                    opacity: 0.8;
                    margin-bottom: 0.5rem;
                }
                .stat-value {
                    font-size: 1.5rem;
                    font-weight: bold;
                }
                .actions {
                    margin-top: 2rem;
                    text-align: center;
                }
                .btn {
                    display: inline-block;
                    padding: 0.75rem 1.5rem;
                    background: rgba(255,255,255,0.2);
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    margin: 0.5rem;
                    transition: all 0.3s;
                    border: none;
                    cursor: pointer;
                    font-size: 1rem;
                }
                .btn:hover {
                    background: rgba(255,255,255,0.3);
                    transform: translateY(-2px);
                }
                .btn-primary {
                    background: rgba(76, 175, 80, 0.3);
                }
                .btn-primary:hover {
                    background: rgba(76, 175, 80, 0.5);
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>🎯 State Management Demo</h1>
                <div class="badge">{$stateType}</div>
                
                <div class="counters">
                    <div class="counter">
                        <div class="counter-label">📝 Request Counter</div>
                        <div class="counter-value">{$requestCount}</div>
                        <div class="counter-desc">Cleared after each request</div>
                    </div>
                    {$persistentHtml}
                </div>
                
                <div class="note">
                    <strong>💡 Note:</strong> {$note}
                </div>
                
                {$statsHtml}
                
                <div class="actions">
                    <button class="btn btn-primary" onclick="location.reload()">🔄 Refresh Page</button>
                    <a href="/" class="btn">🏠 Home</a>
                    <a href="/info" class="btn">ℹ️ System Info</a>
                </div>
            </div>
        </body>
        </html>
        HTML;

        return Response::html($html);
    }
}
