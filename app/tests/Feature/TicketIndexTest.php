<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Tickets\TicketIndex;

class TicketIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_tickets_list()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $tickets = Ticket::factory()->count(5)->create(['created_by' => $user->id]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->assertSee($tickets->first()->title)
            ->assertSee($tickets->last()->title)
            ->assertSee($tickets->first()->ticket_number)
            ->assertSee($tickets->last()->ticket_number);
    }

    /** @test */
    public function it_filters_tickets_by_search()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $ticket1 = Ticket::factory()->create(['title' => 'Login Issue', 'created_by' => $user->id]);
        $ticket2 = Ticket::factory()->create(['title' => 'Database Error', 'created_by' => $user->id]);
        $ticket3 = Ticket::factory()->create(['title' => 'Registration Problem', 'created_by' => $user->id]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->set('search', 'Login')
            ->assertSee($ticket1->title)
            ->assertDontSee($ticket2->title)
            ->assertDontSee($ticket3->title);
    }

    /** @test */
    public function it_filters_tickets_by_status()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $openStatus = TicketStatus::factory()->open()->create();
        $closedStatus = TicketStatus::factory()->closed()->create();

        $openTicket = Ticket::factory()->create([
            'status_id' => $openStatus->id,
            'created_by' => $user->id
        ]);
        $closedTicket = Ticket::factory()->create([
            'status_id' => $closedStatus->id,
            'created_by' => $user->id
        ]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->set('statusFilter', $openStatus->id)
            ->assertSee($openTicket->title)
            ->assertDontSee($closedTicket->title);
    }

    /** @test */
    public function it_filters_tickets_by_priority()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $highPriority = TicketPriority::factory()->create(['name' => 'High']);
        $lowPriority = TicketPriority::factory()->create(['name' => 'Low']);

        $highPriorityTicket = Ticket::factory()->create([
            'priority_id' => $highPriority->id,
            'created_by' => $user->id
        ]);
        $lowPriorityTicket = Ticket::factory()->create([
            'priority_id' => $lowPriority->id,
            'created_by' => $user->id
        ]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->set('priorityFilter', $highPriority->id)
            ->assertSee($highPriorityTicket->title)
            ->assertDontSee($lowPriorityTicket->title);
    }

    /** @test */
    public function it_filters_tickets_by_project()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $ticket1 = Ticket::factory()->create([
            'project_id' => $project1->id,
            'created_by' => $user->id
        ]);
        $ticket2 = Ticket::factory()->create([
            'project_id' => $project2->id,
            'created_by' => $user->id
        ]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->set('projectFilter', $project1->id)
            ->assertSee($ticket1->title)
            ->assertDontSee($ticket2->title);
    }

    /** @test */
    public function it_paginates_tickets()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        Ticket::factory()->count(25)->create(['created_by' => $user->id]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->assertViewHas('tickets', function ($tickets) {
                return $tickets->count() <= 15; // Default pagination
            });
    }

    /** @test */
    public function it_sorts_tickets_by_different_columns()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $ticket1 = Ticket::factory()->create([
            'title' => 'A Ticket',
            'created_by' => $user->id,
            'created_at' => now()->subDays(2)
        ]);
        $ticket2 = Ticket::factory()->create([
            'title' => 'B Ticket',
            'created_by' => $user->id,
            'created_at' => now()->subDay()
        ]);

        // Test sorting by title
        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->set('sortBy', 'title')
            ->set('sortDirection', 'asc')
            ->assertSeeInOrder(['A Ticket', 'B Ticket']);

        // Test sorting by created_at
        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->set('sortBy', 'created_at')
            ->set('sortDirection', 'desc')
            ->assertSeeInOrder(['B Ticket', 'A Ticket']);
    }

    /** @test */
    public function it_can_delete_a_ticket()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');
        $user->givePermissionTo('delete_tickets');

        $ticket = Ticket::factory()->create(['created_by' => $user->id]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->call('deleteTicket', $ticket->id);

        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
    }

    /** @test */
    public function it_cannot_delete_ticket_without_permission()
    {
        $user = User::factory()->create();
        $user->assignRole('Customer');

        $ticket = Ticket::factory()->create(['created_by' => $user->id]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->call('deleteTicket', $ticket->id)
            ->assertForbidden();

        $this->assertNotSoftDeleted('tickets', ['id' => $ticket->id]);
    }

    /** @test */
    public function it_can_bulk_delete_tickets()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');
        $user->givePermissionTo('delete_tickets');

        $tickets = Ticket::factory()->count(3)->create(['created_by' => $user->id]);
        $ticketIds = $tickets->pluck('id')->toArray();

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->set('selectedTickets', $ticketIds)
            ->call('bulkDelete');

        foreach ($ticketIds as $ticketId) {
            $this->assertSoftDeleted('tickets', ['id' => $ticketId]);
        }
    }

    /** @test */
    public function it_can_export_tickets()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');
        $user->givePermissionTo('export_tickets');

        Ticket::factory()->count(5)->create(['created_by' => $user->id]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->call('export')
            ->assertDownload('tickets.xlsx');
    }

    /** @test */
    public function it_shows_only_user_accessible_tickets()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrator');

        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $customer = User::factory()->create();
        $customer->assignRole('Customer');

        // Create tickets for different users
        $adminTicket = Ticket::factory()->create(['created_by' => $admin->id]);
        $employeeTicket = Ticket::factory()->create(['created_by' => $employee->id]);
        $customerTicket = Ticket::factory()->create(['created_by' => $customer->id]);

        // Admin should see all tickets
        Livewire::actingAs($admin)
            ->test(TicketIndex::class)
            ->assertSee($adminTicket->title)
            ->assertSee($employeeTicket->title)
            ->assertSee($customerTicket->title);

        // Employee should see all tickets (employees have broader access)
        Livewire::actingAs($employee)
            ->test(TicketIndex::class)
            ->assertSee($adminTicket->title)
            ->assertSee($employeeTicket->title)
            ->assertSee($customerTicket->title);

        // Customer should only see their own tickets
        Livewire::actingAs($customer)
            ->test(TicketIndex::class)
            ->assertDontSee($adminTicket->title)
            ->assertDontSee($employeeTicket->title)
            ->assertSee($customerTicket->title);
    }

    /** @test */
    public function it_filters_by_date_range()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $oldTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'created_at' => now()->subDays(10)
        ]);
        $recentTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'created_at' => now()->subDay()
        ]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->set('dateFrom', now()->subDays(5)->format('Y-m-d'))
            ->set('dateTo', now()->format('Y-m-d'))
            ->assertDontSee($oldTicket->title)
            ->assertSee($recentTicket->title);
    }

    /** @test */
    public function it_resets_filters()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $ticket = Ticket::factory()->create(['created_by' => $user->id]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->set('search', 'test')
            ->set('statusFilter', 1)
            ->set('priorityFilter', 1)
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('statusFilter', '')
            ->assertSet('priorityFilter', '');
    }

    /** @test */
    public function it_shows_ticket_count()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        Ticket::factory()->count(5)->create(['created_by' => $user->id]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->assertSee('5 tickets');
    }

    /** @test */
    public function it_handles_empty_state()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->assertSee('No tickets found');
    }

    /** @test */
    public function it_validates_search_input()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->set('search', str_repeat('a', 300)) // Too long
            ->assertHasErrors(['search' => 'max']);
    }

    /** @test */
    public function it_updates_ticket_status_inline()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');
        $user->givePermissionTo('edit_tickets');

        $ticket = Ticket::factory()->create(['created_by' => $user->id]);
        $newStatus = TicketStatus::factory()->inProgress()->create();

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->call('updateTicketStatus', $ticket->id, $newStatus->id);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status_id' => $newStatus->id
        ]);
    }

    /** @test */
    public function it_shows_overdue_tickets_with_special_styling()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $overdueTicket = Ticket::factory()->create([
            'created_by' => $user->id,
            'due_date' => now()->subDays(1)
        ]);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->assertSee($overdueTicket->title)
            ->assertSee('overdue'); // Assuming overdue tickets have special styling
    }

    /** @test */
    public function it_handles_large_number_of_tickets_efficiently()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        // Create a large number of tickets
        Ticket::factory()->count(100)->create(['created_by' => $user->id]);

        $startTime = microtime(true);

        Livewire::actingAs($user)
            ->test(TicketIndex::class)
            ->assertSuccessful();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Should load within reasonable time (less than 2 seconds)
        $this->assertLessThan(2, $executionTime);
    }
}
