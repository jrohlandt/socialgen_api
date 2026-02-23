<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpenAILog>
 */
class OpenAILogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'user_id'
            'model' => 'gpt-4o-mini-2024-07-18',
            'input' => json_encode(['some_input' => 'some_value']),
            'output' => json_encode(['some_input' => 'some_value']),
            'token_usage' => fake()->numberBetween(100, 500),
        ];
    }
}
