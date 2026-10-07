<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Http\Routing\Contracts\RouterInterface;
use Psr\Log\LoggerInterface;
use Witals\Framework\Application;

/**
 * RouterFactory — decides which Router strategy to boot.
 *
 * Called ONCE at provider register() → bound as a singleton.
 * On RoadRunner, this decision is made once per worker lifetime.
 * On Traditional servers, it is made once per process.
 *
 * Strategy selection:
 *   - Multilingual active  → LocalizedRouter  (locale-prefix aware)
 *   - Single language      → Router           (standard, zero overhead)
 */
class RouterFactory
{
    public static function create(Application $app, LoggerInterface $logger): RouterInterface
    {
        $locales        = config('app.locales', []);
        $defaultLocale  = config('app.locale', env('APP_LOCALE', 'en'));
        $isMultilingual = count($locales) > 1;

        $logger->info("RouterFactory: locales={locales} isMultilingual={multilingual}", [
            'locales' => implode(',', $locales),
            'multilingual' => $isMultilingual ? 'YES' : 'NO',
        ]);

        if ($isMultilingual) {
            return new LocalizedRouter($app, $logger, $locales, $defaultLocale);
        }

        return new Router($app, $logger);
    }
}
