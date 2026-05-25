<?php
//
declare(strict_types=1);

use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\Web\Blog\BlogController;
use App\Http\Controllers\Web\Certification\CertificationController;
use App\Http\Controllers\Web\CertifiedCenter\CertifiedCenterController;
use App\Http\Controllers\Web\Home\HomeController;
use App\Http\Controllers\Web\Locale\LocaleController;
use App\Http\Controllers\Web\StaticPage\StaticPageController;
use App\Http\Controllers\Web\Trainer\TrainerController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/web')->name('home');

Route::prefix('web')->name('web.')->group(function (): void {

    Route::get('/', HomeController::class)->name('home');

    Route::get('/lang/{locale}', LocaleController::class)
        ->name('locale');

    Route::get('/pages/{slug}', [StaticPageController::class, 'show'])
        ->name('pages.show');

    Route::prefix('certifications')->name('certifications.')->group(function (): void {
        Route::get('/', [CertificationController::class, 'index'])
            ->name('index');
        Route::get('/search', [CertificationController::class, 'search'])
            ->name('search');
        Route::get('/{serial}', [CertificationController::class, 'show'])
            ->name('show');
    });

    Route::prefix('centers')->name('centers.')->group(function (): void {
        Route::get('/', [CertifiedCenterController::class, 'index'])
            ->name('index');
        Route::get('/{id}', [CertifiedCenterController::class, 'show'])
            ->name('show');
    });

    Route::prefix('trainers')->name('trainers.')->group(function (): void {
        Route::get('/', [TrainerController::class, 'index'])
            ->name('index');
        Route::get('/evaluation', [TrainerController::class, 'evaluation'])
            ->name('evaluation');
        Route::get('/{trainer}', [TrainerController::class, 'show'])
            ->name('show');
    });

    Route::prefix('blog')->name('blog.')->group(function (): void {
        Route::get('/', [BlogController::class, 'index'])
            ->name('index');
        Route::get('/{slug}', [BlogController::class, 'show'])
            ->name('show');
    });

    Route::get('/health', HealthCheckController::class)->name('health');
});

require __DIR__ . '/admin.php';
