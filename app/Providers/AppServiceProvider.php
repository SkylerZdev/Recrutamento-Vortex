<?php

namespace App\Providers;

use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach (['candidate' => Candidate::class, 'vacancy' => Vacancy::class] as $parameter => $model) {
            Route::bind($parameter, static function (string $value) use ($model): Candidate|Vacancy {
                $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                abort_if($id === false, 404);

                return $model::findOrFail($id);
            });
        }
    }
}
