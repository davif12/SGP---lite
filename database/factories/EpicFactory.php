<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['backlog', 'in_progress', 'done']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'project_id' => Project::factory(),
        ];
    }
}
