<?php

declare(strict_types=1);

namespace Modules\SchemaOrg\Services;

use App\Foundation\Application;
use App\Foundation\Modules\ModuleContext;
use Modules\Seo\Services\SeoService;

/**
 * SchemaOrgService — builds schema.org JSON-LD for the current entity and
 * exposes it through the shared `render.head` hook.
 *
 * Reads the entity context (set by legacy/site.php) and, when available, the
 * SEO row (description / og_image) so structured data stays in sync with the
 * SEO module. Output is empty when there is nothing meaningful to describe,
 * preserving legacy parity.
 */
class SchemaOrgService
{
    public function __construct(
        private readonly Application $app,
        private readonly ModuleContext $context,
        private readonly ?SeoService $seo = null,
    ) {
    }

    /**
     * Hook: render.head — returns one or more <script type="application/ld+json">
     * blocks describing the current page.
     */
    public function renderHead(mixed $payload = []): string
    {
        $graph = $this->buildGraph();
        if ($graph === []) {
            return '';
        }

        $json = json_encode(
            ['@context' => 'https://schema.org', '@graph' => $graph],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );

        return '<script type="application/ld+json">' . $json . '</script>' . "\n";
    }

    /**
     * Assemble the @graph nodes for the current request.
     *
     * @return array<int, array<string, mixed>>
     */
    public function buildGraph(): array
    {
        $entity = $this->context->get('seo.entity');
        if (!is_array($entity)) {
            $entity = $this->context->get('entity');
        }
        if (!is_array($entity)) {
            return [];
        }

        $site = $this->context->get('site', []);
        $siteName = is_array($site) && isset($site['name']) ? (string) $site['name'] : '';
        $siteUrl = is_array($site) && isset($site['url']) ? (string) $site['url'] : '';

        $url = isset($entity['url']) ? (string) $entity['url'] : '';
        $type = isset($entity['type']) ? (string) $entity['type'] : '';
        $title = isset($entity['title']) ? (string) $entity['title'] : '';

        $seo = $this->seo?->findCurrent() ?? [];
        $description = (string) ($seo['description'] ?? '');
        $image = (string) ($seo['og_image'] ?? '');

        $graph = [];

        // Site-level entities (WebSite + Organization) on every page.
        if ($siteName !== '' || $siteUrl !== '') {
            $graph[] = array_filter([
                '@type' => 'WebSite',
                '@id'   => $siteUrl !== '' ? rtrim($siteUrl, '/') . '/#website' : null,
                'name'  => $siteName ?: null,
                'url'   => $siteUrl ?: null,
            ]);
        }

        $pageNode = $this->buildPageNode($type, $url, $title, $description, $image, $siteName);
        if ($pageNode !== null) {
            $graph[] = $pageNode;
        }

        $breadcrumbs = $this->buildBreadcrumbs($entity, $url, $siteUrl);
        if ($breadcrumbs !== null) {
            $graph[] = $breadcrumbs;
        }

        // Drop null entries and reindex.
        return array_values(array_filter($graph, static fn ($n) => is_array($n) && $n !== []));
    }

    /**
     * @param  array<string, mixed> $entity
     * @return array<string, mixed>|null
     */
    private function buildPageNode(string $type, string $url, string $title, string $description, string $image, string $siteName): ?array
    {
        $type = strtolower($type);
        $node = null;

        if ($type === 'product') {
            $node = array_filter([
                '@type'       => 'Product',
                '@id'         => $url !== '' ? $url . '#product' : null,
                'name'        => $title ?: null,
                'description' => $description ?: null,
                'image'       => $image ?: null,
                'url'         => $url ?: null,
            ]);
        } elseif (in_array($type, ['article', 'news', 'tin-tuc', 'newsarticle'], true)) {
            $node = array_filter([
                '@type'            => 'Article',
                '@id'              => $url !== '' ? $url . '#article' : null,
                'headline'         => $title ?: null,
                'description'      => $description ?: null,
                'image'            => $image ?: null,
                'mainEntityOfPage' => $url ?: null,
                'publisher'        => $siteName !== '' ? ['@type' => 'Organization', 'name' => $siteName] : null,
            ]);
        } else {
            $node = array_filter([
                '@type'       => 'WebPage',
                '@id'         => $url !== '' ? $url . '#webpage' : null,
                'name'        => $title ?: null,
                'description' => $description ?: null,
                'url'         => $url ?: null,
            ]);
        }

        return $node ?: null;
    }

    /**
     * @param  array<string, mixed> $entity
     * @return array<string, mixed>|null
     */
    private function buildBreadcrumbs(array $entity, string $url, string $siteUrl): ?array
    {
        $trail = $entity['breadcrumbs'] ?? null;
        if (!is_array($trail) || $trail === []) {
            return null;
        }

        $items = [];
        $pos = 1;
        foreach ($trail as $crumb) {
            if (!is_array($crumb)) {
                continue;
            }
            $name = (string) ($crumb['name'] ?? '');
            $itemUrl = (string) ($crumb['url'] ?? '');
            if ($name === '') {
                continue;
            }
            $items[] = array_filter([
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => $name,
                'item'     => $itemUrl !== '' ? $itemUrl : null,
            ]);
        }

        if ($items === []) {
            return null;
        }

        return array_filter([
            '@type'           => 'BreadcrumbList',
            '@id'             => $url !== '' ? $url . '#breadcrumb' : null,
            'itemListElement' => $items,
        ]);
    }
}