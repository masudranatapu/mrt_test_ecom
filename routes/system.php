<?php

use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| System tools (no terminal hosting)
|--------------------------------------------------------------------------
|
| Loaded WITHOUT the "web" middleware group: no session and no database is
| needed, so the page opens even when the database connection is broken.
| Protected by SYSTEM_KEY (.env). See SystemController.
|
*/

Route::get('/', [SystemController::class, 'index'])->name('index');
// No throttle middleware: it uses the cache store, which lives in the database
Route::post('/', [SystemController::class, 'run'])->name('run');
