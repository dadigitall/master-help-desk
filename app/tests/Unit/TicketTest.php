<?php

namespace Tests\Unit;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Models\Comment;
use App\Models\Chat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_ticket()
    {
        $project = Project::factory()->create();
        $status = TicketStatus::factory()->create();
        $priority = TicketPriority::factory()->create();
        $type = TicketType::factory()->create();
        $assignedTo = User::factory()->create();
        $createdBy = User::factory()->create();

        $ticketData = [
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket description',
            'project_id' => $project->id,
            'status_id' => $status->id,
            'priority_id' => $priority->id,
            'type_id' => $type->id,
            'assigned_to' => $assignedTo->id,
            'created_by' => $createdBy->id,
            'ticket_number' => 'TICKET-2025-1001',
            'estimated_hours' => 8.5,
            'actual_hours' => 6.0,
            'due_date' => now()->addDays(7),
        ];

        $ticket = Ticket::create($ticketData);

        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertEquals('Test Ticket', $ticket->title);
        $this->assertEquals('This is a test ticket description', $ticket->description);
        $this->assertEquals($project->id, $ticket->project_id);
        $this->assertEquals($status->id, $ticket->status_id);
        $this->assertEquals($priority->id, $ticket->priority_id);
        $this->assertEquals($type->id, $ticket->type_id);
        $this->assertEquals($assignedTo->id, $ticket->assigned_to);
        $this->assertEquals($createdBy->id, $ticket->created_by);
        $this->assertEquals('TICKET-2025-1001', $ticket->ticket_number);
        $this->assertEquals(8.5, $ticket->estimated_hours);
        $this->assertEquals(6.0, $ticket->actual_hours);
    }

    /** @test */
    public function it_can_soft_delete_a_ticket()
    {
        $ticket = Ticket::factory()->create();
        $ticketId = $ticket->id;

        $ticket->delete();

        $this->assertSoftDeleted('tickets', ['id' => $ticketId]);
        $this->assertTrue($ticket->trashed());
    }

    /** @test */
    public function it_can_restore_a_soft_deleted_ticket()
    {
        $ticket = Ticket::factory()->create();
        $ticket->delete();

        $ticket->restore();

        $this->assertNotSoftDeleted('tickets', ['id' => $ticket->id]);
        $this->assertFalse($ticket->trashed());
    }

    /** @test */
    public function it_can_scope_to_active_tickets()
    {
        $activeTicket = Ticket::factory()->create();
        $deletedTicket = Ticket::factory()->create();
        $deletedTicket->delete();

        $activeTickets = Ticket::active()->get();

        $this->assertCount(1, $activeTickets);
        $this->assertTrue($activeTickets->contains($activeTicket));
        $this->assertFalse($activeTickets->contains($deletedTicket));
    }

    /** @test */
    public function it_can_scope_to_open_tickets()
    {
        $openTicket = Ticket::factory()->open()->create();
        $closedTicket = Ticket::factory()->closed()->create();

        $openTickets = Ticket::open()->get();

        $this->assertCount(1, $openTickets);
        $this->assertTrue($openTickets->contains($openTicket));
        $this->assertFalse($openTickets->contains($closedTicket));
    }

    /** @test */
    public function it_can_scope_to_overdue_tickets()
    {
        $overdueTicket = Ticket::factory()->overdue()->create();
        $futureTicket = Ticket::factory()->create(['due_date' => now()->addDays(7)]);

        $overdueTickets = Ticket::overdue()->get();

        $this->assertCount(1, $overdueTickets);
        $this->assertTrue($overdueTickets->contains($overdueTicket));
        $this->assertFalse($overdueTickets->contains($futureTicket));
    }

    /** @test */
    public function it_can_scope_to_high_priority_tickets()
    {
        $highPriorityTicket = Ticket::factory()->highPriority()->create();
        $lowPriorityTicket = Ticket::factory()->lowPriority()->create();

        $highPriorityTickets = Ticket::highPriority()->get();

        $this->assertCount(1, $highPriorityTickets);
        $this->assertTrue($highPriorityTickets->contains($highPriorityTicket));
        $this->assertFalse($highPriorityTickets->contains($lowPriorityTicket));
    }

    /** @test */
    public function it_belong_to_project()
    {
        $ticket = Ticket::factory()->create();
        $project = $ticket->project;

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($project->id, $ticket->project_id);
    }

    /** @test */
    public function it_belong_to_assigned_user()
    {
        $ticket = Ticket::factory()->create();
        $assignedUser = $ticket->assignedUser;

        $this->assertInstanceOf(User::class, $assignedUser);
        $this->assertEquals($assignedUser->id, $ticket->assigned_to);
    }

    /** @test */
    public function it_belong_to_creator()
    {
        $ticket = Ticket::factory()->create();
        $creator = $ticket->creator;

        $this->assertInstanceOf(User::class, $creator);
        $this->assertEquals($creator->id, $ticket->created_by);
    }

    /** @test */
    public function it_belong_to_status()
    {
        $ticket = Ticket::factory()->create();
        $status = $ticket->status;

        $this->assertInstanceOf(TicketStatus::class, $status);
        $this->assertEquals($status->id, $ticket->status_id);
    }

    /** @test */
    public function it_belong_to_priority()
    {
        $ticket = Ticket::factory()->create();
        $priority = $ticket->priority;

        $this->assertInstanceOf(TicketPriority::class, $priority);
        $this->assertEquals($priority->id, $ticket->priority_id);
    }

    /** @test */
    public function it_belong_to_type()
    {
        $ticket = Ticket::factory()->create();
        $type = $ticket->type;

        $this->assertInstanceOf(TicketType::class, $type);
        $this->assertEquals($type->id, $ticket->type_id);
    }

    /** @test */
    public function it_can_have_comments()
    {
        $ticket = Ticket::factory()->create();
        $comment1 = Comment::factory()->create(['ticket_id' => $ticket->id]);
        $comment2 = Comment::factory()->create(['ticket_id' => $ticket->id]);

        $comments = $ticket->comments;

        $this->assertCount(2, $comments);
        $this->assertTrue($comments->contains($comment1));
        $this->assertTrue($comments->contains($comment2));
    }

    /** @test */
    public function it_can_have_chat()
    {
        $ticket = Ticket::factory()->create();
        $message1 = Chat::factory()->create(['ticket_id' => $ticket->id]);
        $message2 = Chat::factory()->create(['ticket_id' => $ticket->id]);

        $messages = $ticket->chatMessages;

        $this->assertCount(2, $messages);
        $this->assertTrue($messages->contains($message1));
        $this->assertTrue($messages->contains($message2));
    }

    /** @test */
    public function it_can_check_if_is_overdue()
    {
        $overdueTicket = Ticket::factory()->create(['due_date' => now()->subDays(1)]);
        $futureTicket = Ticket::factory()->create(['due_date' => now()->addDays(1)]);

        $this->assertTrue($overdueTicket->isOverdue());
        $this->assertFalse($futureTicket->isOverdue());
    }

    /** @test */
    public function it_can_check_if_is_resolved()
    {
        $resolvedTicket = Ticket::factory()->resolved()->create();
        $openTicket = Ticket::factory()->open()->create();

        $this->assertTrue($resolvedTicket->isResolved());
        $this->assertFalse($openTicket->isResolved());
    }

    /** @test */
    public function it_can_check_if_is_closed()
    {
        $closedTicket = Ticket::factory()->closed()->create();
        $openTicket = Ticket::factory()->open()->create();

        $this->assertTrue($closedTicket->isClosed());
        $this->assertFalse($openTicket->isClosed());
    }

    /** @test */
    public function it_can_resolve_a_ticket()
    {
        $ticket = Ticket::factory()->open()->create();
        $resolvedStatus = TicketStatus::factory()->resolved()->create();

        $ticket->resolve();

        $this->assertEquals($resolvedStatus->id, $ticket->status_id);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertTrue($ticket->isResolved());
    }

    /** @test */
    public function it_can_close_a_ticket()
    {
        $ticket = Ticket::factory()->open()->create();
        $closedStatus = TicketStatus::factory()->closed()->create();

        $ticket->close();

        $this->assertEquals($closedStatus->id, $ticket->status_id);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertNotNull($ticket->closed_at);
        $this->assertTrue($ticket->isClosed());
    }

    /** @test */
    public function it_can_reopen_a_ticket()
    {
        $ticket = Ticket::factory()->closed()->create();
        $openStatus = TicketStatus::factory()->open()->create();

        $ticket->reopen();

        $this->assertEquals($openStatus->id, $ticket->status_id);
        $this->assertNull($ticket->resolved_at);
        $this->assertNull($ticket->closed_at);
        $this->assertFalse($ticket->isResolved());
        $this->assertFalse($ticket->isClosed());
    }

    /** @test */
    public function it_can_get_time_remaining()
    {
        $futureTicket = Ticket::factory()->create(['due_date' => now()->addDays(2)]);
        $overdueTicket = Ticket::factory()->create(['due_date' => now()->subDays(1)]);

        $this->assertEquals(2, $futureTicket->getTimeRemaining());
        $this->assertEquals(-1, $overdueTicket->getTimeRemaining());
    }

    /** @test */
    public function it_can_get_hours_overdue()
    {
        $overdue1Day = Ticket::factory()->create(['due_date' => now()->subDays(1)]);
        $overdue2Hours = Ticket::factory()->create(['due_date' => now()->subHours(2)]);
        $futureTicket = Ticket::factory()->create(['due_date' => now()->addHours(2)]);

        $this->assertEquals(24, $overdue1Day->getHoursOverdue());
        $this->assertEquals(2, $overdue2Hours->getHoursOverdue());
        $this->assertEquals(0, $futureTicket->getHoursOverdue());
    }

    /** @test */
    public function it_can_get_progress_percentage()
    {
        $ticket = Ticket::factory()->create([
            'estimated_hours' => 10,
            'actual_hours' => 5,
        ]);

        $this->assertEquals(50, $ticket->getProgressPercentage());

        $ticket->actual_hours = 12;
        $this->assertEquals(100, $ticket->getProgressPercentage());

        $ticket->estimated_hours = 0;
        $this->assertEquals(0, $ticket->getProgressPercentage());
    }

    /** @test */
    public function it_can_search_tickets_by_title()
    {
        $ticket1 = Ticket::factory()->create(['title' => 'Login Issue']);
        $ticket2 = Ticket::factory()->create(['title' => 'Registration Problem']);
        $ticket3 = Ticket::factory()->create(['title' => 'Database Error']);

        $results = Ticket::search('Login')->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($ticket1));
        $this->assertFalse($results->contains($ticket2));
        $this->assertFalse($results->contains($ticket3));
    }

    /** @test */
    public function it_can_search_tickets_by_description()
    {
        $ticket1 = Ticket::factory()->create(['description' => 'User cannot login to the system']);
        $ticket2 = Ticket::factory()->create(['description' => 'Database connection failed']);
        $ticket3 = Ticket::factory()->create(['description' => 'Email notifications not working']);

        $results = Ticket::search('login')->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($ticket1));
        $this->assertFalse($results->contains($ticket2));
        $this->assertFalse($results->contains($ticket3));
    }

    /** @test */
    public function it_can_search_tickets_by_number()
    {
        $ticket1 = Ticket::factory()->create(['ticket_number' => 'TICKET-2025-1001']);
        $ticket2 = Ticket::factory()->create(['ticket_number' => 'TICKET-2025-1002']);
        $ticket3 = Ticket::factory()->create(['ticket_number' => 'TICKET-2025-1003']);

        $results = Ticket::search('1001')->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($ticket1));
        $this->assertFalse($results->contains($ticket2));
        $this->assertFalse($results->contains($ticket3));
    }

    /** @test */
    public function it_can_get_formatted_due_date()
    {
        $ticket = Ticket::factory()->create(['due_date' => '2025-12-25 15:30:00']);

        $this->assertEquals('Dec 25, 2025', $ticket->getFormattedDueDate());
    }

    /** @test */
    public function it_can_check_if_user_can_view_ticket()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrator');

        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $customer = User::factory()->create();
        $customer->assignRole('Customer');

        $ticket = Ticket::factory()->createdBy($customer)->assignedTo($employee)->create();

        $this->assertTrue($admin->canViewTicket($ticket));
        $this->assertTrue($employee->canViewTicket($ticket));
        $this->assertTrue($customer->canViewTicket($ticket));
    }

    /** @test */
    public function it_can_check_if_user_can_edit_ticket()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrator');

        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $customer = User::factory()->create();
        $customer->assignRole('Customer');

        $ticket = Ticket::factory()->createdBy($customer)->assignedTo($employee)->create();

        $this->assertTrue($admin->canEditTicket($ticket));
        $this->assertTrue($employee->canEditTicket($ticket));
        $this->assertFalse($customer->canEditTicket($ticket));
    }
}
