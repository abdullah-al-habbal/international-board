<?php
// filePath: routes\admin.php
declare(strict_types=1);

use App\Http\Controllers\Admin\StatExportController;
use Illuminate\Support\Facades\Route;

Route::get('/exports/{type}', [StatExportController::class, 'download'])
    ->name('admin.exports.download')
    ->middleware(['web', 'auth']);
