<?php

use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/vagas');
Route::view('/entrar', 'login')->name('login')->middleware('guest');
Route::post('/entrar', [SessionController::class, 'store'])->middleware(['guest', 'throttle:6,1,login']);
Route::middleware('auth')->group(function () {
    Route::post('/sair', [SessionController::class, 'destroy'])->name('logout');
    require __DIR__.'/recruitment.php';
});
