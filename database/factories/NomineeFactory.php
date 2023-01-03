<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Nominee>
 */
class NomineeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name=fake()->name();
        return [
            'name' => $name,
            'position_id' => fake()->numberBetween(1,4),
            'affiliation' => fake()->word(),
            'slug' => Str::of($name)->slug('_'),
        ];
    }
}
