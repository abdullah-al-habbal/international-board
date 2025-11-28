<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Certification\CertificationController;
use App\Http\Controllers\Web\CertifiedCenter\CertifiedCenterController;
use App\Http\Controllers\Web\StaticPage\StaticPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('pages')->group(function () {
    Route::get('/{slug}', [StaticPageController::class, 'show'])->name('pages.show');
});

Route::prefix('certifications')->group(function () {
    Route::get('/search', [CertificationController::class, 'checkout'])->name('certifications.search');
    Route::get('/{serial}', [CertificationController::class, 'show'])->name('certifications.show');
});

Route::prefix('centers')->group(function () {
    Route::get('/', [CertifiedCenterController::class, 'index'])->name('centers.index');
    Route::get('/{id}', [CertifiedCenterController::class, 'show'])->name('centers.show');
});
