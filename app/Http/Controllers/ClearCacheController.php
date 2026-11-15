<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

/**
 * Clears Laravel's caches from a browser, for hosts without SSH: /clear-cache?key=CACHE_CLEAR_KEY.
 * Runs optimize:clear (config, routes, views, events, compiled files and the cache store). Add &optimize=1
 * to rebuild the config, route and view caches afterwards. Answers 404 while CACHE_CLEAR_KEY is empty.
 */
class ClearCacheController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $key = (string) config('app.cache_clear_key');

        abort_if($key === '' || ! hash_equals($key, (string) $request->query('key')), 404);

        $commands = $request->boolean('optimize') ? ['optimize:clear', 'optimize'] : ['optimize:clear'];

        $output = collect($commands)
            ->map(function (string $command): string {
                $exitCode = Artisan::call($command);

                return '$ php artisan '.$command.($exitCode === 0 ? '  [OK]' : "  [FAILED: exit {$exitCode}]")
                    ."\n".trim(Artisan::output());
            })
            ->implode("\n\n");

        return response($output."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'no-store',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
