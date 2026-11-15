<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Runs the setup Artisan commands from a browser, for hosts without SSH (e.g. InfinityFree):
 *
 *     /maintenance?key=MAINTENANCE_KEY&run=check,migrate,link,optimize
 *
 * Commands run in the order given. The route skips the session and rate-limit middleware so it still
 * works on an empty database, and answers a bare 404 while MAINTENANCE_KEY is empty or the key is wrong.
 */
class MaintenanceController extends Controller
{
    /**
     * @var array<string, array{0: string, 1: array<string, bool>, 2: string}>
     */
    private const COMMANDS = [
        'clear' => ['optimize:clear', [], 'Clear config, route, view, event and app caches'],
        'migrate' => ['migrate', ['--force' => true], 'Create or update the database tables'],
        'seed' => ['db:seed', ['--force' => true], 'Create the admin account (first deploy only)'],
        'link' => ['storage:link', [], 'Link public/storage so uploaded photos show'],
        'optimize' => ['optimize', [], 'Rebuild config, route and view caches (faster site)'],
    ];

    public function __invoke(Request $request): Response
    {
        $key = (string) config('app.maintenance_key');

        if ($key === '' || ! hash_equals($key, (string) $request->query('key'))) {
            return response('Not Found', 404);
        }

        $requested = array_values(array_filter(array_map('trim', explode(',', (string) $request->query('run')))));

        $output = $requested === []
            ? $this->usage()
            : collect($requested)->map(fn (string $name): string => $this->run($name))->implode("\n\n");

        return response($output."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'no-store',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    private function run(string $name): string
    {
        if ($name === 'check') {
            return "== check\n".$this->check();
        }

        if (! isset(self::COMMANDS[$name])) {
            return "== {$name}\n[SKIPPED] Unknown command. Use one of: check, ".implode(', ', array_keys(self::COMMANDS)).'.';
        }

        [$command, $options] = self::COMMANDS[$name];

        try {
            $exitCode = Artisan::call($command, $options);
            $status = $exitCode === 0 ? '[OK]' : "[FAILED: exit {$exitCode}]";

            return "== {$name}: php artisan {$command}  {$status}\n".trim(Artisan::output());
        } catch (Throwable $exception) {
            return "== {$name}: php artisan {$command}  [FAILED]\n".$exception->getMessage()
                ."\n(".str_replace(base_path(), '', $exception->getFile()).':'.$exception->getLine().')';
        }
    }

    /**
     * What the server has and lacks: PHP, extensions, .env values, database, folders and build files.
     */
    private function check(): string
    {
        $lines = [];
        $mark = fn (bool $ok): string => $ok ? '[OK]  ' : '[FAIL]';

        $lines[] = $mark(PHP_VERSION_ID >= 80401).' PHP '.PHP_VERSION.' (needs 8.4.1 or newer)';

        foreach (['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'fileinfo', 'curl'] as $extension) {
            $lines[] = $mark(extension_loaded($extension))." PHP extension {$extension}";
        }

        $lines[] = $mark(config('app.key') !== null && config('app.key') !== '').' APP_KEY is set';
        $lines[] = $mark(! config('app.debug')).' APP_DEBUG is false (debug: '.(config('app.debug') ? 'true' : 'false').')';
        $lines[] = '[INFO] APP_ENV='.config('app.env').'  APP_URL='.config('app.url');

        try {
            DB::connection()->getPdo();
            $lines[] = '[OK]   Database connection ('.config('database.connections.mysql.database').')';

            foreach (['migrations', 'users', 'sessions', 'cache', 'auth_tokens'] as $table) {
                $lines[] = $mark(Schema::hasTable($table))." Table {$table}".(Schema::hasTable($table) ? '' : ' is missing: run migrate');
            }
        } catch (Throwable $exception) {
            $lines[] = '[FAIL] Database connection: '.$exception->getMessage();
        }

        foreach (['storage/framework/views', 'storage/framework/cache/data', 'storage/framework/sessions', 'storage/logs', 'bootstrap/cache'] as $folder) {
            $path = base_path($folder);
            $lines[] = $mark(is_dir($path) && is_writable($path))." Writable {$folder}".(is_dir($path) ? '' : ' (missing)');
        }

        $lines[] = $mark(is_file(public_path('build/manifest.json'))).' public/build/manifest.json (run npm run build and upload public/build)';
        $lines[] = $mark(file_exists(public_path('storage'))).' public/storage link (run link)';
        $lines[] = $mark(is_dir(base_path('vendor'))).' vendor folder uploaded';

        return implode("\n", $lines);
    }

    private function usage(): string
    {
        $lines = ['Add &run=... with one or more commands, comma-separated, run in order:', '', '  check     Show what the server has and what is missing'];

        foreach (self::COMMANDS as $name => [$command, , $description]) {
            $lines[] = '  '.str_pad($name, 10).str_pad($description, 55)."(php artisan {$command})";
        }

        return implode("\n", [...$lines, '', 'First deploy:  &run=check,migrate,seed,link,optimize', 'After update:  &run=clear,migrate,optimize']);
    }
}
