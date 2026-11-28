<?php

declare(strict_types=1);

namespace Modules\Home\Sections;

use App\Foundation\Application;
use Cycle\Database\DatabaseProviderInterface;

/**
 * Reproduces the legacy home: reads category rows with vitri 'cathome' and
 * renders each one by its data_type (product, news, video, album, info,
 * service, article, logo...). Handlers are pluggable: pass a map
 * data_type => callable(row, context): string to override the default
 * view-based rendering (home/cathome/<data_type>.php).
 */
class CathomeSection implements SectionInterface
{
    /** @var array<string, callable(array<string,mixed>, array<string,mixed>): string> */
    private array $handlers = [];

    public function __construct(private readonly Application $app)
    {
    }

    public function getType(): string
    {
        return 'cathome';
    }

    /** @param array<string, callable> $handlers */
    public function register(string $dataType, callable $handler): void
    {
        $this->handlers[$dataType] = $handler;
    }

    public function render(array $data, array $context): string
    {
        $rows = [];
        try {
            $rows = $this->app->make(DatabaseProviderInterface::class)->database()
                ->select()->from($this->table('category'))
                ->where('active', 1)
                ->where('vitri', 'LIKE', '%:cathome:%')
                ->orderBy('thu_tu', 'ASC')
                ->orderBy('id_category', 'ASC')
                ->fetchAll();
        } catch (\Throwable) {
            $rows = [];
        }

        $factory = $this->app->make(\Witals\Framework\Contracts\View\Factory::class);
        $db = $this->app->make(DatabaseProviderInterface::class)->database();
        $out = '';
        foreach ($rows as $row) {
            $type = (string) ($row['data_type'] ?? '');
            if ($type === '' || (int) ($row['id_category'] ?? 0) <= 0) {
                continue;
            }
            if (isset($this->handlers[$type])) {
                $out .= ($this->handlers[$type])($row, $context);
                continue;
            }
            try {
                $out .= $factory->make('home/cathome/' . $type, array_merge($context, [
                    'row'  => $row,
                    'cat'  => $row,
                    'data' => $data,
                    'db'   => $db,
                ]))->render();
            } catch (\Throwable) {
                // no handler/view for this data_type → skip
            }
        }

        return $out;
    }

    private function table(string $logical): string
    {
        $override = $this->app->config('database.tables.' . $logical);

        return is_string($override) && $override !== '' ? $override : $logical;
    }
}