<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\SystemActionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PDO;
use Throwable;

/**
 * Browser-based maintenance for hosting without a terminal (cPanel etc.).
 *
 * Runs WITHOUT the session / database middleware (see routes/system.php), so it
 * still works when the database is not reachable — which is exactly when it is
 * needed. Protected by SYSTEM_KEY in .env.
 */
class SystemController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(system_key_valid($request->query('key')), 404);

        return view('system.index', [
            'key' => $request->query('key'),
            'checks' => $this->checks(),
            'result' => null,
        ]);
    }

    public function run(SystemActionRequest $request): View
    {
        $result = match ($request->action) {
            'clear-cache' => $this->clearCache(),
            'storage-link' => $this->storageLink(),
            'migrate' => $this->artisan('migrate', ['--force' => true]),
            'seed' => $this->artisan('db:seed', ['--force' => true]),
        };

        return view('system.index', [
            'key' => $request->key,
            'checks' => $this->checks(),
            'result' => ['action' => $request->action] + $result,
        ]);
    }

    /**
     * Clears compiled config / routes / views. Works even if the database
     * (used by the cache store) is down.
     */
    private function clearCache(): array
    {
        $log = [];

        foreach (glob(base_path('bootstrap/cache/*.php')) ?: [] as $file) {
            if (in_array(basename($file), ['config.php', 'events.php']) || str_starts_with(basename($file), 'routes-')) {
                @unlink($file);
                $log[] = 'Deleted bootstrap/cache/'.basename($file);
            }
        }

        foreach (['config:clear', 'route:clear', 'view:clear', 'event:clear', 'cache:clear'] as $command) {
            try {
                Artisan::call($command);
                $log[] = "{$command}: done";
            } catch (Throwable $e) {
                $log[] = "{$command}: skipped ({$e->getMessage()})";
            }
        }

        return ['ok' => true, 'output' => implode("\n", $log)];
    }

    private function storageLink(): array
    {
        if (file_exists(public_path('storage'))) {
            return ['ok' => true, 'output' => 'public/storage already exists.'];
        }

        $result = $this->artisan('storage:link');

        if (! file_exists(public_path('storage'))) {
            $result['ok'] = false;
            $result['output'] .= "\n\nThe host may block symlinks. In cPanel File Manager you can instead copy the folder"
                ." storage/app/public to public/storage (re-copy after uploading new images).";
        }

        return $result;
    }

    private function artisan(string $command, array $options = []): array
    {
        try {
            $code = Artisan::call($command, $options);

            return ['ok' => $code === 0, 'output' => trim(Artisan::output()) ?: 'Done.'];
        } catch (Throwable $e) {
            return ['ok' => false, 'output' => $e->getMessage()];
        }
    }

    /**
     * Health checks shown on the page.
     *
     * @return array<string, mixed>
     */
    private function checks(): array
    {
        $connection = config('database.connections.'.config('database.default'));

        // 1. Can the user log in to MySQL at all, and which databases may it see?
        $server = ['ok' => false, 'databases' => [], 'error' => null];
        try {
            $pdo = new PDO(
                "mysql:host={$connection['host']};port={$connection['port']}",
                $connection['username'],
                $connection['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
            );
            $server['ok'] = true;
            $server['databases'] = array_values(array_diff(
                $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN),
                ['information_schema', 'performance_schema']
            ));
        } catch (Throwable $e) {
            $server['error'] = $e->getMessage();
        }

        // 2. Can Laravel open the configured database?
        $database = ['ok' => false, 'error' => null, 'tables' => 0, 'pending' => []];
        try {
            DB::connection()->getPdo();
            $database['ok'] = true;
            $database['tables'] = count(DB::select('SHOW TABLES'));

            $migrator = app('migrator');
            $ran = $migrator->repositoryExists() ? $migrator->getRepository()->getRan() : [];
            $files = array_keys($migrator->getMigrationFiles(database_path('migrations')));
            $database['pending'] = array_values(array_diff($files, $ran));
        } catch (Throwable $e) {
            $database['error'] = $e->getMessage();
        }

        return [
            'php' => PHP_VERSION,
            'laravel' => app()->version(),
            'env' => config('app.env'),
            'debug' => (bool) config('app.debug'),
            'url' => config('app.url'),
            'db_name' => $connection['database'] ?? null,
            'db_user' => $connection['username'] ?? null,
            'db_host' => $connection['host'] ?? null,
            'server' => $server,
            'database' => $database,
            'config_cached' => app()->configurationIsCached(),
            'storage_link' => file_exists(public_path('storage')),
            'writable' => [
                'storage' => is_writable(storage_path()),
                'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            ],
            'build' => file_exists(public_path('build/manifest.json')),
        ];
    }
}
