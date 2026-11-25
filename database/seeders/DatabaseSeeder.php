<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $open = Vacancy::factory(22)->create();
        Vacancy::factory(4)->create(['paused' => true]);
        Candidate::factory(32)->create()->each(function (Candidate $candidate) use ($open) {
            $candidate->vacancies()->attach($open->random(2)->modelKeys());
        });
    }
}
