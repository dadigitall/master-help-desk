<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(5),
            'project_id' => Project::factory(),
            'status_id' => TicketStatus::factory(),
            'priority_id' => TicketPriority::factory(),
            'type_id' => TicketType::factory(),
            'assigned_to' => User::factory(),
            'created_by' => User::factory(),
            'ticket_number' => $this->generateTicketNumber(),
            'estimated_hours' => $this->faker->randomFloat(1, 1, 40),
            'actual_hours' => $this->faker->randomFloat(1, 0, 50),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'resolved_at' => null,
            'closed_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Generate a unique ticket number.
     */
    private function generateTicketNumber(): string
    {
        $prefix = 'TICKET';
        $year = date('Y');
        $sequence = $this->faker->unique()->numberBetween(1000, 9999);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    /**
     * Indicate that the ticket is open.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => TicketStatus::where('name', 'Open')->first() ?? TicketStatus::factory()->create(['name' => 'Open']),
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    /**
     * Indicate that the ticket is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => TicketStatus::where('name', 'In Progress')->first() ?? TicketStatus::factory()->create(['name' => 'In Progress']),
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    /**
     * Indicate that the ticket is resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => TicketStatus::where('name', 'Resolved')->first() ?? TicketStatus::factory()->create(['name' => 'Resolved']),
            'resolved_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'closed_at' => null,
        ]);
    }

    /**
     * Indicate that the ticket is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => TicketStatus::where('name', 'Closed')->first() ?? TicketStatus::factory()->create(['name' => 'Closed']),
            'resolved_at' => $this->faker->dateTimeBetween('-2 weeks', '-1 week'),
            'closed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Indicate that the ticket has high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_id' => TicketPriority::where('name', 'High')->first() ?? TicketPriority::factory()->create(['name' => 'High']),
        ]);
    }

    /**
     * Indicate that the ticket has low priority.
     */
    public function lowPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_id' => TicketPriority::where('name', 'Low')->first() ?? TicketPriority::factory()->create(['name' => 'Low']),
        ]);
    }

    /**
     * Indicate that the ticket is a bug.
     */
    public function bug(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_id' => TicketType::where('name', 'Bug')->first() ?? TicketType::factory()->create(['name' => 'Bug']),
            'title' => 'Bug: ' . $this->faker->sentence(4),
        ]);
    }

    /**
     * Indicate that the ticket is a feature request.
     */
    public function feature(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_id' => TicketType::where('name', 'Feature')->first() ?? TicketType::factory()->create(['name' => 'Feature']),
            'title' => 'Feature: ' . $this->faker->sentence(4),
        ]);
    }

    /**
     * Indicate that the ticket is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    /**
     * Indicate that the ticket is for a specific project.
     */
    public function forProject(Project $project): static
    {
        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
        ]);
    }

    /**
     * Indicate that the ticket is assigned to a specific user.
     */
    public function assignedTo(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => $user->id,
        ]);
    }

    /**
     * Indicate that the ticket was created by a specific user.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }
}
