<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'content' => $this->faker->paragraph(2),
            'user_id' => User::factory(),
            'task_id' => Task::factory(),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Create a recent comment (within 15 minutes)
     */
    public function recent()
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => now()->subMinutes(rand(1, 14)),
            ];
        });
    }

    /**
     * Create an old comment (older than 15 minutes)
     */
    public function old()
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => now()->subMinutes(rand(16, 1440)), // 16 minutes to 24 hours
            ];
        });
    }

    /**
     * Create a long comment
     */
    public function long()
    {
        return $this->state(function (array $attributes) {
            return [
                'content' => $this->faker->paragraphs(5, true),
            ];
        });
    }
}
