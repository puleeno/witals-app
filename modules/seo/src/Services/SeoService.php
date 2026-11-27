<?php

declare(strict_types=1);

namespace Modules\Seo\Services;

use App\Foundation\Application;
use App\Foundation\Modules\ModuleContext;
use Cycle\Database\DatabaseProviderInterface;

/**
 * SeoService — reads seo_meta rows and renders <head> tags.
 *
 * Table name is overridable via config('database.tables.seo_meta').
 * All DB access is defensive: if the table is missing or the query fails,
 * renderHead() returns '' so the legacy output is untouched (parity).
 */
class SeoService
{
    public function __construct(
        private readonly Application $app,
        private readonly ModuleContext $context,
        private readonly DatabaseProviderInterface $dbal,
    ) {
    }

    private function table(): string
    {
        $override = $this->app->config('database.tables.seo_meta');

        return is_string($override) && $override !== '' ? $override : 'seo_meta';
    }

    /**
     * Fetch the SEO row for the current request.
     * Resolution order: explicit entity (type+id) → url.
     *
     * @return array<string, mixed>|null
     */
    public function findCurrent(): ?array
    {
        $entity = $this->context->get('seo.entity');
        if (!is_array($entity)) {
            $entity = $this->context->get('entity');
        }

        $url = is_array($entity) && isset($entity['url']) ? (string) $entity['url'] : '';

        try {
            $db = $this->dbal->database();
            $table = $this->table();

            if (is_array($entity) && !empty($entity['type']) && !empty($entity['id'])) {
                $row = $db->select()
                    ->from($table)
                    ->where('entity_type', (string) $entity['type'])
                    ->where('entity_id', (int) $entity['id'])
                    ->limit(1)
                    ->fetchAll();

                if (!empty($row)) {
                    return $row[0];
                }
            }

            if ($url !== '') {
                $row = $db->select()
                    ->from($table)
                    ->where('url', $url)
                    ->limit(1)
                    ->fetchAll();

                if (!empty($row)) {
                    return $row[0];
                }
            }
        } catch (\Throwable) {
            // Table missing / DB unavailable → no SEO output.
        }

        return null;
    }

    /**
     * Hook: render.head — returns the <head> markup for the current entity.
     * Empty string when there is no SEO row (legacy output preserved).
     */
    public function renderHead(mixed $payload = []): string
    {
        $seo = $this->findCurrent();
        if ($seo === null) {
            return '';
        }

        $site = $this->context->get('site', []);
        $siteName = is_array($site) && isset($site['name']) ? (string) $site['name'] : '';

        $title = trim((string) ($seo['title'] ?? ''));
        $description = trim((string) ($seo['description'] ?? ''));
        $canonical = trim((string) ($seo['canonical'] ?? ''));
        $ogImage = trim((string) ($seo['og_image'] ?? ''));
        $robots = trim((string) ($seo['robots'] ?? ''));

        $entity = $this->context->get('seo.entity');
        if (!is_array($entity)) {
            $entity = $this->context->get('entity');
        }
        $url = is_array($entity) && isset($entity['url']) ? (string) $entity['url'] : '';

        $out = '';

        if ($canonical !== '') {
            $out .= '<link rel="canonical" href="' . htmlspecialchars($canonical, ENT_QUOTES) . '">' . "\n";
        }
        if ($robots !== '') {
            $out .= '<meta name="robots" content="' . htmlspecialchars($robots, ENT_QUOTES) . '">' . "\n";
        }

        // Open Graph
        $out .= '<meta property="og:type" content="website">' . "\n";
        if ($title !== '') {
            $out .= '<meta property="og:title" content="' . htmlspecialchars($title, ENT_QUOTES) . '">' . "\n";
        }
        if ($description !== '') {
            $out .= '<meta property="og:description" content="' . htmlspecialchars($description, ENT_QUOTES) . '">' . "\n";
        }
        if ($url !== '') {
            $out .= '<meta property="og:url" content="' . htmlspecialchars($url, ENT_QUOTES) . '">' . "\n";
        }
        if ($siteName !== '') {
            $out .= '<meta property="og:site_name" content="' . htmlspecialchars($siteName, ENT_QUOTES) . '">' . "\n";
        }
        if ($ogImage !== '') {
            $out .= '<meta property="og:image" content="' . htmlspecialchars($ogImage, ENT_QUOTES) . '">' . "\n";
        }

        // Twitter card
        $out .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        if ($title !== '') {
            $out .= '<meta name="twitter:title" content="' . htmlspecialchars($title, ENT_QUOTES) . '">' . "\n";
        }
        if ($description !== '') {
            $out .= '<meta name="twitter:description" content="' . htmlspecialchars($description, ENT_QUOTES) . '">' . "\n";
        }
        if ($ogImage !== '') {
            $out .= '<meta name="twitter:image" content="' . htmlspecialchars($ogImage, ENT_QUOTES) . '">' . "\n";
        }

        return $out;
    }

    /**
     * Hook: edit.fields — describes the SEO fields for dashboard edit screens.
     * Other modules (dashboard) embed these into their edit forms.
     *
     * @return array<int, array<string, mixed>>
     */
    public function editFields(mixed $payload = []): array
    {
        return [
            ['name' => 'title',       'label' => 'Tiêu đề SEO',      'type' => 'text',     'max' => 255],
            ['name' => 'description', 'label' => 'Mô tả SEO',        'type' => 'textarea', 'max' => 160],
            ['name' => 'keywords',    'label' => 'Từ khóa',          'type' => 'text',     'max' => 255],
            ['name' => 'canonical',   'label' => 'Canonical URL',    'type' => 'url',      'max' => 255],
            ['name' => 'og_image',    'label' => 'Ảnh OG',           'type' => 'url',      'max' => 255],
            ['name' => 'robots',      'label' => 'Robots',           'type' => 'select',   'options' => ['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow']],
        ];
    }
}