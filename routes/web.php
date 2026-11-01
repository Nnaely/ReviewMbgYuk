<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;

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

    Route::post('/notifikasi/{notification}/baca', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])
        ->middleware('role:pelapor')
        ->name('notifications.read');

    Route::get('/laporan', [\App\Http\Controllers\ReportController::class, 'index'])
        ->middleware('role:pelapor')
        ->name('reports.index');

    Route::get('/laporan/{report}/edit', [\App\Http\Controllers\ReportController::class, 'edit'])
        ->middleware('role:pelapor')
        ->name('reports.edit');

    Route::patch('/laporan/{report}', [\App\Http\Controllers\ReportController::class, 'correct'])
        ->middleware('role:pelapor')
        ->name('reports.correct');

    Route::post('/laporan', [\App\Http\Controllers\ReportController::class, 'store'])
        ->middleware(['role:pelapor', 'throttle:10,1'])
        ->name('reports.store');

    Route::get('/sekolah/{school}/distribusi', [\App\Http\Controllers\DistributionController::class, 'bySchool'])
        ->middleware('role:pelapor')
        ->name('schools.distributions');

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

    Route::post('/distribusi', [\App\Http\Controllers\DistributionController::class, 'store'])
        ->middleware('role:koordinator')
        ->name('distributions.store');

    Route::get('/distribusi/{distribution}/edit', [\App\Http\Controllers\DistributionController::class, 'edit'])
        ->middleware('role:koordinator')
        ->name('distributions.edit');

    Route::patch('/distribusi/{distribution}', [\App\Http\Controllers\DistributionController::class, 'update'])
        ->middleware('role:koordinator')
        ->name('distributions.update');

    Route::delete('/distribusi/{distribution}', [\App\Http\Controllers\DistributionController::class, 'destroy'])
        ->middleware('role:koordinator')
        ->name('distributions.destroy');

    Route::get('/sekolah', [\App\Http\Controllers\SchoolController::class, 'index'])
        ->middleware('role:koordinator')
        ->name('schools.index');

    Route::post('/sekolah', [\App\Http\Controllers\SchoolController::class, 'store'])
        ->middleware('role:koordinator')
        ->name('schools.store');

    Route::get('/sekolah/{school}/edit', [\App\Http\Controllers\SchoolController::class, 'edit'])
        ->middleware('role:koordinator')
        ->name('schools.edit');

    Route::patch('/sekolah/{school}', [\App\Http\Controllers\SchoolController::class, 'update'])
        ->middleware('role:koordinator')
        ->name('schools.update');

    Route::get('/area/pelapor', [AreaController::class, 'pelapor'])
        ->middleware('role:pelapor')
        ->name('area.pelapor');

    Route::get('/area/pemantau', [AreaController::class, 'pemantau'])
        ->middleware('role:pemantau')
        ->name('area.pemantau');

    Route::get('/area/koordinator', [AreaController::class, 'koordinator'])
        ->middleware('role:koordinator')
        ->name('area.koordinator');
});
