<?php

declare(strict_types=1);

namespace Modules\AutoIndex\Services;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;

/**
 * AutoIndexService — submits URLs to the Google Index API and records the
 * result in the `auto_index` table (name overridable via
 * config('database.tables.auto_index')).
 *
 * Credentials come from config('services.auto_index.*'):
 *   - service_account_json : path to a Google service-account JSON key
 *   - indexing_enabled     : bool master switch (default false)
 *
 * Everything is defensive: without credentials or the table, submit() is a
 * safe no-op that records "skipped" and never throws.
 */
class AutoIndexService
{
    public function __construct(
        private readonly Application $app,
        private readonly DatabaseProviderInterface $dbal,
    ) {
    }

    private function table(): string
    {
        $override = $this->app->config('database.tables.auto_index');

        return is_string($override) && $override !== '' ? $override : 'auto_index';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->app->config('services.auto_index.indexing_enabled', false);
    }

    /**
     * Submit a single URL. Returns a status array:
     * ['status' => 'ok|skipped|error', 'response' => string].
     *
     * @return array{status: string, response: string}
     */
    public function submit(string $url, string $entityType = '', int $entityId = 0): array
    {
        $url = trim($url);
        if ($url === '') {
            return ['status' => 'error', 'response' => 'empty url'];
        }

        if (!$this->isEnabled()) {
            $this->record($url, $entityType, $entityId, 'skipped', 'indexing disabled');

            return ['status' => 'skipped', 'response' => 'indexing disabled'];
        }

        $token = $this->accessToken();
        if ($token === null) {
            $this->record($url, $entityType, $entityId, 'skipped', 'missing credentials');

            return ['status' => 'skipped', 'response' => 'missing credentials'];
        }

        try {
            $payload = json_encode([
                'url'       => $url,
                'notificationType' => 'URL_UPDATED',
            ], JSON_UNESCAPED_SLASHES);

            $ch = curl_init('https://indexing.googleapis.com/v3/urlNotifications:publish');
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $token,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_TIMEOUT        => 15,
            ]);
            $body = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);

            if ($body === false) {
                $this->record($url, $entityType, $entityId, 'error', $err ?: 'curl error');

                return ['status' => 'error', 'response' => $err ?: 'curl error'];
            }

            $ok = $code >= 200 && $code < 300;
            $this->record($url, $entityType, $entityId, $ok ? 'ok' : 'error', (string) $body);

            return ['status' => $ok ? 'ok' : 'error', 'response' => (string) $body];
        } catch (\Throwable $e) {
            $this->record($url, $entityType, $entityId, 'error', $e->getMessage());

            return ['status' => 'error', 'response' => $e->getMessage()];
        }
    }

    /**
     * Obtain a short-lived OAuth2 access token from the service-account JSON.
     */
    private function accessToken(): ?string
    {
        $path = $this->app->config('services.auto_index.service_account_json');
        if (!is_string($path) || $path === '' || !is_file($path)) {
            return null;
        }

        try {
            $json = json_decode((string) file_get_contents($path), true);
            if (!is_array($json) || empty($json['private_key']) || empty($json['client_email'])) {
                return null;
            }

            $now = time();
            $header = $this->b64(['alg' => 'RS256', 'typ' => 'JWT']);
            $claims = $this->b64([
                'iss'   => $json['client_email'],
                'scope' => 'https://www.googleapis.com/auth/indexing',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]);
            $sig = $this->b64(hash('sha256', $header . '.' . $claims, true), true);
            $jwt = $header . '.' . $claims . '.' . $sig;

            $ch = curl_init('https://oauth2.googleapis.com/token');
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS     => http_build_query([
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ]),
                CURLOPT_TIMEOUT        => 15,
            ]);
            $body = curl_exec($ch);
            curl_close($ch);

            $data = json_decode((string) $body, true);

            return is_array($data) && !empty($data['access_token']) ? (string) $data['access_token'] : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function b64(array $data, bool $raw = false): string
    {
        $json = $raw ? $data['raw'] ?? '' : json_encode($data, JSON_UNESCAPED_SLASHES);

        return rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
    }

    /**
     * Upsert the submission record. Never throws.
     */
    private function record(string $url, string $entityType, int $entityId, string $status, string $response): void
    {
        try {
            $db = $this->dbal->database();
            $table = $this->table();
            $now = new \DateTimeImmutable('now');

            $existing = $db->select()->from($table)->where('url', $url)->limit(1)->fetchAll();

            if (!empty($existing)) {
                $db->update($table, [
                    'status'           => $status,
                    'response'         => $response,
                    'last_submitted_at'=> $now,
                ], ['url' => $url])->run();

                return;
            }

            $db->insert($table, [
                'url'              => $url,
                'entity_type'      => $entityType ?: null,
                'entity_id'        => $entityId ?: null,
                'status'           => $status,
                'response'         => $response,
                'last_submitted_at'=> $now,
            ])->run();
        } catch (\Throwable) {
            // Table missing / DB unavailable — swallow.
        }
    }
}