<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;

foreach (['vagas' => [VacancyController::class, 'vacancy'], 'candidatos' => [CandidateController::class, 'candidate']] as $path => [$controller, $parameter]) {
    Route::delete($path.'/lote', [$controller, 'bulkDestroy'])->name($path.'.bulk');
    $resource = Route::resource($path, $controller)->parameters([$path => $parameter]);
    if (Route::getLastGroupPrefix() === 'api') {
        $resource->except(['create', 'edit']);
    }
}
Route::post('vagas/{vacancy}/inscricoes', [EnrollmentController::class, 'store'])->name('inscricoes.store');
