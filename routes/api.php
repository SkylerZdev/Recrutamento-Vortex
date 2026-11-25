<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->middleware(['throttle:60,1', 'auth.basic.once'])->group(function () {
    require __DIR__.'/recruitment.php';
});
