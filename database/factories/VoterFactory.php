<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voter>
 */
class VoterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $studentNumber=fake()->numerify('####-#####-MN-#');
        return [
            'student_number' => $studentNumber,
            'password' => Hash::make('1234'),
            'email' => fake()->email(),
            'mobile' => fake()->phoneNumber(),
            'has_password_request' => fake()->boolean(),
            'slug' => $studentNumber,
        ];
    }
}
