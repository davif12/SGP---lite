<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statuses = ['todo', 'in_progress', 'review', 'done'];
        $priorities = ['low', 'medium', 'high', 'critical'];
        
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional(0.7)->paragraph(),
            'status' => $this->faker->randomElement($statuses),
            'priority' => $this->faker->randomElement($priorities),
            'story_points' => $this->faker->optional(0.6)->numberBetween(1, 13),
            'position' => $this->faker->numberBetween(0, 100),
            'due_date' => $this->faker->optional(0.4)->dateTimeBetween('now', '+2 months'),
            'epic_id' => \App\Models\Epic::factory(),
            'assigned_to' => $this->faker->optional(0.5)->passthrough(\App\Models\User::factory()),
        ];
    }
}
