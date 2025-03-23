<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Language;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Snippet>
 */
class SnippetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;
        $languageId = Language::inRandomOrder()->first()->id ?? Language::factory()->create()->id;
        
        return [
            'user_id' => $userId,
            'title' => fake()->sentence(),
            'code_content' => fake()->paragraph(),
            'language_id' => $languageId,
            'description' => fake()->optional(0.7)->paragraph(),
        ];
    }
}
