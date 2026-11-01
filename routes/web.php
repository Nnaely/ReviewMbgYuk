<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])
        ->middleware('throttle:5,1');
});

Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/', function () {
    return view('welcome');
})->name('beranda');

Route::middleware('auth')->group(function () {
    Route::get('/laporan/{report}/bukti', [\App\Http\Controllers\ReportController::class, 'photo'])
        ->name('reports.photo');

    Route::get('/laporan', [\App\Http\Controllers\ReportController::class, 'index'])
        ->middleware('role:pelapor')
        ->name('reports.index');

    Route::post('/laporan', [\App\Http\Controllers\ReportController::class, 'store'])
        ->middleware(['role:pelapor', 'throttle:10,1'])
        ->name('reports.store');

    Route::get('/laporan-masuk', [\App\Http\Controllers\ReportController::class, 'inbox'])
        ->middleware('role:pemantau,koordinator')
        ->name('reports.inbox');

    Route::get('/laporan-masuk/{report}', [\App\Http\Controllers\ReportController::class, 'show'])
        ->middleware('role:pemantau,koordinator')
        ->name('reports.show');

    Route::patch('/laporan-masuk/{report}', [\App\Http\Controllers\ReportController::class, 'update'])
        ->middleware('role:pemantau,koordinator')
        ->name('reports.update');

    Route::get('/distribusi', [\App\Http\Controllers\DistributionController::class, 'index'])
        ->middleware('role:pemantau,koordinator')
        ->name('distributions.index');

    Route::view('/area/pelapor', 'areas.role', ['namaPeran' => 'Pelapor'])
        ->middleware('role:pelapor')
        ->name('area.pelapor');

    Route::view('/area/pemantau', 'areas.role', ['namaPeran' => 'Pemantau'])
        ->middleware('role:pemantau')
        ->name('area.pemantau');

    Route::view('/area/koordinator', 'areas.role', ['namaPeran' => 'Koordinator'])
        ->middleware('role:koordinator')
        ->name('area.koordinator');
});
