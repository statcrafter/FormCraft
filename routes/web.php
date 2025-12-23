<?php

use App\Http\Controllers\FormController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('forms', FormController::class);
    Route::post('forms/import', [FormController::class, 'import'])->name('forms.import');
    Route::get('forms/{form}/download', [FormController::class, 'download'])->name('forms.download');
    Route::post('forms/{form}/assets', [FormController::class, 'uploadAsset'])->name('forms.assets.upload');
    Route::delete('forms/{form}/assets/{asset}', [FormController::class, 'deleteAsset'])->name('forms.assets.delete');
});

require __DIR__.'/settings.php';
