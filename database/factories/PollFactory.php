<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Poll>
 */
class PollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        static $number = 1;

        return [
            'title' => 'Student Election '. fake()->dateTimeBetween('-6 weeks')->format('Y-m-d'),
            'description' => fake()->sentence(),
            'start' => fake()->date(),
            'duration' => fake()->numberBetween(1,3),
            'status' =>$number++,
            'category' =>fake()->numberBetween(1,2),
        ];
    }
}
