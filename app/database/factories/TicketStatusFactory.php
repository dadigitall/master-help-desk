<?php

namespace Database\Factories;

use App\Models\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketStatus>
 */
class TicketStatusFactory extends Factory
{
    protected $model = TicketStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Open', 'In Progress', 'Resolved', 'Closed', 'On Hold', 'Cancelled']),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
            'order' => $this->faker->numberBetween(1, 10),
            'is_default' => false,
            'is_final' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the status is the default one.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Open',
            'is_default' => true,
            'order' => 1,
        ]);
    }

    /**
     * Indicate that the status is final (closed).
     */
    public function final(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Closed',
            'is_final' => true,
            'order' => 99,
        ]);
    }

    /**
     * Create an "Open" status.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Open',
            'color' => '#10B981',
            'order' => 1,
            'is_default' => true,
            'is_final' => false,
        ]);
    }

    /**
     * Create an "In Progress" status.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'In Progress',
            'color' => '#F59E0B',
            'order' => 2,
            'is_default' => false,
            'is_final' => false,
        ]);
    }

    /**
     * Create a "Resolved" status.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Resolved',
            'color' => '#3B82F6',
            'order' => 3,
            'is_default' => false,
            'is_final' => false,
        ]);
    }

    /**
     * Create a "Closed" status.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Closed',
            'color' => '#6B7280',
            'order' => 4,
            'is_default' => false,
            'is_final' => true,
        ]);
    }
}
