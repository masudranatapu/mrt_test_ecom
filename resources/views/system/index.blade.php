{{-- Standalone page: no session, no Vite, no database needed --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>System Tools</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; color: #111827; font-size: 15px; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 24px 16px 48px; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        .muted { color: #6b7280; font-size: 13px; }
        .card { background: #fff; border-radius: 12px; padding: 18px 20px; margin-top: 16px; box-shadow: 0 1px 2px rgba(0,0,0,.05); }
        .card h2 { font-size: 16px; margin: 0 0 12px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 7px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        td:first-child { color: #6b7280; width: 42%; }
        .ok { color: #15803d; font-weight: 600; }
        .bad { color: #b91c1c; font-weight: 600; }
        .warn { color: #b45309; font-weight: 600; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 12px; }
        .action { border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; display: flex; flex-direction: column; gap: 8px; }
        .action p { margin: 0; font-size: 13px; color: #4b5563; flex: 1; }
        button { border: 0; border-radius: 8px; padding: 9px 14px; font-weight: 600; cursor: pointer; background: #0d6efd; color: #fff; font-size: 14px; }
        button.danger { background: #dc2626; }
        button.dark { background: #111827; }
        pre { background: #0f172a; color: #e2e8f0; padding: 14px; border-radius: 10px; overflow-x: auto; white-space: pre-wrap; font-size: 13px; margin: 0; }
        .result-ok { border-left: 4px solid #16a34a; }
        .result-bad { border-left: 4px solid #dc2626; }
        .tip { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 14px; font-size: 14px; margin-top: 10px; }
        .tip ol { margin: 6px 0 0; padding-left: 20px; }
        code { background: #f1f5f9; padding: 1px 5px; border-radius: 4px; font-size: 13px; }
    </style>
</head>

<body>
    <div class="wrap">
        <h1>System Tools</h1>
        <div class="muted">Maintenance without a terminal. Keep this URL private. Remove <code>SYSTEM_KEY</code> from .env when you no longer need it.</div>

        @if ($result)
            <div class="card {{ $result['ok'] ? 'result-ok' : 'result-bad' }}">
                <h2>{{ $result['ok'] ? '✔' : '✖' }} {{ ucfirst(str_replace('-', ' ', $result['action'])) }}</h2>
                <pre>{{ $result['output'] }}</pre>
            </div>
        @endif

        {{-- Database --}}
        <div class="card">
            <h2>Database</h2>
            <table>
                <tr><td>Host / Database / User</td><td><code>{{ $checks['db_host'] }}</code> / <code>{{ $checks['db_name'] }}</code> / <code>{{ $checks['db_user'] }}</code></td></tr>
                <tr>
                    <td>MySQL login (username + password)</td>
                    <td>
                        @if ($checks['server']['ok'])
                            <span class="ok">OK</span>
                        @else
                            <span class="bad">Failed</span><br><span class="muted">{{ $checks['server']['error'] }}</span>
                        @endif
                    </td>
                </tr>
                @if ($checks['server']['ok'])
                    <tr>
                        <td>Databases this user can access</td>
                        <td>
                            @forelse ($checks['server']['databases'] as $db)
                                <code>{{ $db }}</code>{{ $db === $checks['db_name'] ? ' ✔' : '' }}<br>
                            @empty
                                <span class="bad">None</span>
                            @endforelse
                        </td>
                    </tr>
                @endif
                <tr>
                    <td>Open database "{{ $checks['db_name'] }}"</td>
                    <td>
                        @if ($checks['database']['ok'])
                            <span class="ok">OK</span> — {{ $checks['database']['tables'] }} tables
                        @else
                            <span class="bad">Failed</span><br><span class="muted">{{ $checks['database']['error'] }}</span>
                        @endif
                    </td>
                </tr>
                @if ($checks['database']['ok'])
                    <tr>
                        <td>Pending migrations</td>
                        <td>
                            @if (count($checks['database']['pending']))
                                <span class="warn">{{ count($checks['database']['pending']) }} pending</span> — run "Migrate" below
                            @else
                                <span class="ok">None</span>
                            @endif
                        </td>
                    </tr>
                @endif
            </table>

            @if ($checks['server']['ok'] && ! in_array($checks['db_name'], $checks['server']['databases'], true))
                <div class="tip">
                    <strong>The user cannot see the database "{{ $checks['db_name'] }}".</strong> In cPanel:
                    <ol>
                        <li>Open <b>MySQL® Databases</b>.</li>
                        <li>Check the exact name under <b>Current Databases</b> and use it as <code>DB_DATABASE</code> in .env.</li>
                        <li>Under <b>Add User To Database</b> choose user <code>{{ $checks['db_user'] }}</code> and the database, click <b>Add</b>, tick <b>ALL PRIVILEGES</b>, then <b>Make Changes</b>.</li>
                        <li>Reload this page.</li>
                    </ol>
                </div>
            @elseif (! $checks['server']['ok'])
                <div class="tip">
                    <strong>MySQL rejected the username or password.</strong> Check <code>DB_USERNAME</code> / <code>DB_PASSWORD</code> in .env against cPanel → MySQL® Databases → Current Users. Try <code>DB_HOST=localhost</code> if it is <code>127.0.0.1</code>.
                </div>
            @endif
        </div>

        {{-- Application --}}
        <div class="card">
            <h2>Application</h2>
            <table>
                <tr><td>PHP / Laravel</td><td>{{ $checks['php'] }} / {{ $checks['laravel'] }}</td></tr>
                <tr>
                    <td>APP_ENV / APP_DEBUG</td>
                    <td>
                        {{ $checks['env'] }} /
                        @if ($checks['debug'])
                            <span class="bad">true — set APP_DEBUG=false on a live site</span>
                        @else
                            <span class="ok">false</span>
                        @endif
                    </td>
                </tr>
                <tr><td>APP_URL</td><td>{{ $checks['url'] }}</td></tr>
                <tr><td>Config cached</td><td>{!! $checks['config_cached'] ? '<span class="warn">Yes — click "Clear cache" after editing .env</span>' : '<span class="ok">No</span>' !!}</td></tr>
                <tr><td>public/storage link</td><td>{!! $checks['storage_link'] ? '<span class="ok">OK</span>' : '<span class="warn">Missing — click "Storage link"</span>' !!}</td></tr>
                <tr><td>Built assets (public/build)</td><td>{!! $checks['build'] ? '<span class="ok">OK</span>' : '<span class="bad">Missing — upload the public/build folder</span>' !!}</td></tr>
                @foreach ($checks['writable'] as $dir => $writable)
                    <tr><td>{{ $dir }} writable</td><td>{!! $writable ? '<span class="ok">Yes</span>' : '<span class="bad">No — set folder permission to 775</span>' !!}</td></tr>
                @endforeach
            </table>
        </div>

        {{-- Actions --}}
        <div class="card">
            <h2>Actions</h2>
            <div class="grid">
                @foreach ([
                    ['action' => 'clear-cache', 'title' => 'Clear cache', 'text' => 'Clear config, route and view cache. Do this after every .env change.', 'class' => 'dark', 'confirm' => null],
                    ['action' => 'storage-link', 'title' => 'Storage link', 'text' => 'Create public/storage so uploaded images are visible.', 'class' => '', 'confirm' => null],
                    ['action' => 'migrate', 'title' => 'Migrate', 'text' => 'Create / update database tables. Safe to run again; existing data is kept.', 'class' => '', 'confirm' => 'Run database migrations?'],
                    ['action' => 'seed', 'title' => 'Seed demo data', 'text' => 'Add the demo admin, staff, customers, products, FAQs and promotions. Not needed if you imported the SQL file.', 'class' => 'danger', 'confirm' => 'Add demo data to this database?'],
                ] as $item)
                    <form method="POST" action="{{ route('system.run') }}" class="action"
                        @if ($item['confirm']) onsubmit="return confirm('{{ $item['confirm'] }}')" @endif>
                        <input type="hidden" name="key" value="{{ $key }}">
                        <input type="hidden" name="action" value="{{ $item['action'] }}">
                        <strong>{{ $item['title'] }}</strong>
                        <p>{{ $item['text'] }}</p>
                        <button type="submit" class="{{ $item['class'] }}">Run</button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
</body>

</html>
