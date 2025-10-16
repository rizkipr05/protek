<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DivisionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle(),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
