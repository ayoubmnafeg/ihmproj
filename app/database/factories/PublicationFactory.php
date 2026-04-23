<?php

namespace Database\Factories;

use App\Models\Publication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publication>
 */
class PublicationFactory extends Factory
{
    protected $model = Publication::class;

    public function definition(): array
    {
        return [
            'title'      => fake()->sentence(6),
            'text'       => fake()->paragraphs(3, true),
            'media_type' => fake()->optional(0.3)->randomElement(['image', 'video', 'audio']),
        ];
    }
}
