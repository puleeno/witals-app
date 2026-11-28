<?php

declare(strict_types=1);

namespace Modules\Home\Sections;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;

/**
 * Reproduces the legacy home (home.htm) exactly: reads category rows with
 * vitri 'cathome' and groups them by data_type, then renders the single
 * home/cathome/index.php view (same markup/CSS/JS as the old site).
 *
 * Handlers are pluggable: register(dataType, callable(row, context)) to
 * override the default grouping for a data_type.
 */
class CathomeSection implements SectionInterface
{
    /** @var array<string, callable(array<string,mixed>, array<string,mixed>): array<int, mixed>> */
    private array $handlers = [];

    public function __construct(private readonly Application $app)
    {
    }

    public function getType(): string
    {
        return 'cathome';
    }

    /** @param callable(array<string,mixed>, array<string,mixed>): array<int,mixed> $handler */
    public function register(string $dataType, callable $handler): void
    {
        $this->handlers[$dataType] = $handler;
    }

    public function render(array $data, array $context): string
    {
        $db = $this->app->make(DatabaseProviderInterface::class)->database();

        $rows = [];
        try {
            $rows = $db->select()->from($this->table('category'))
                ->where('active', 1)
                ->where('vitri', 'LIKE', '%:cathome:%')
                ->orderBy('thu_tu', 'ASC')
                ->orderBy('id_category', 'ASC')
                ->fetchAll();
        } catch (\Throwable) {
            $rows = [];
        }

        $groups = ['product' => [], 'news' => [], 'video' => [], 'other' => []];
        foreach ($rows as $row) {
            $type = (string) ($row['data_type'] ?? '');
            if ($type === '' || (int) ($row['id_category'] ?? 0) <= 0) {
                continue;
            }
            if (isset($this->handlers[$type])) {
                $groups['other'][] = ($this->handlers[$type])($row, $context);
                continue;
            }
            if ($type === 'product') {
                $groups['product'][] = ['cat' => $row, 'items' => $this->products($db, (int) $row['id_category'])];
            } elseif ($type === 'news') {
                $groups['news'][] = ['cat' => $row, 'items' => $this->news($db, (int) $row['id_category'])];
            } elseif ($type === 'video') {
                $groups['video'][] = ['cat' => $row, 'items' => $this->videos($db, (int) $row['id_category'])];
            } else {
                $groups['other'][] = ['cat' => $row, 'items' => []];
            }
        }

        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);

        return $factory->make('home/cathome/index', array_merge($context, $groups + ['data' => $data]))->render();
    }

    /** @return array<int, array<string, mixed>> */
    private function products(\Cycle\Database\Database $db, int $idc): array
    {
        $ids = $this->parentIds($db, $idc);
        $orig = $idc;
        $all = $db->select()->from($this->table('product'))->where('active', 1)->orderBy('thu_tu', 'ASC')->limit(500)->fetchAll();
        $out = [];
        foreach ($all as $p) {
            $pc = (int) ($p['id_category'] ?? 0);
            $gc = (string) ($p['groupcat'] ?? '');
            if (in_array($pc, $ids, true) || ($orig > 0 && strpos($gc, ':' . $orig . ':') !== false)) {
                $out[] = $p;
            }
        }

        return $out;
    }

    /** @return array<int, array<string, mixed>> */
    private function news(\Cycle\Database\Database $db, int $idc): array
    {
        $ids = $this->parentIds($db, $idc);
        $orig = $idc;
        $all = $db->select()->from($this->table('news'))->where('active', 1)->orderBy('id_news', 'DESC')->limit(500)->fetchAll();
        $out = [];
        foreach ($all as $n) {
            $nc = (int) ($n['id_category'] ?? 0);
            $gc = (string) ($n['groupcat'] ?? '');
            if (in_array($nc, $ids, true) || ($orig > 0 && strpos($gc, ':' . $orig . ':') !== false)) {
                $out[] = $n;
            }
        }

        return array_slice($out, 0, 6);
    }

    /** @return array<int, array<string, mixed>> */
    private function videos(\Cycle\Database\Database $db, int $idc): array
    {
        try {
            return $db->select()->from('video')->where('active', 1)->orderBy('thu_tu', 'DESC')->limit(3)->fetchAll();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, int> */
    private function parentIds(\Cycle\Database\Database $db, int $idc): array
    {
        $ids = [$idc];
        $guard = 0;
        while ($guard++ < 10) {
            $pr = $db->select()->from($this->table('category'))->where('id_category', $idc)->limit(1)->fetchAll();
            $pid = (int) ($pr[0]['parentid'] ?? 0);
            if ($pid <= 0 || in_array($pid, $ids, true)) {
                break;
            }
            $ids[] = $pid;
            $idc = $pid;
        }

        return $ids;
    }

    private function table(string $logical): string
    {
        $override = $this->app->config('database.tables.' . $logical);

        return is_string($override) && $override !== '' ? $override : $logical;
    }
}