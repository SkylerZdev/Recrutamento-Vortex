<?php

namespace Database\Factories;

use App\Enums\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

class VacancyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'contract' => fake()->randomElement(Contract::cases())->value,
            'paused' => false,
        ];
    }
}
