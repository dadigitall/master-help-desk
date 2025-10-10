<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\FavoriteProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'phone' => '+1234567890',
            'department' => 'IT',
            'position' => 'Developer',
        ];

        $user = User::create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('+1234567890', $user->phone);
        $this->assertEquals('IT', $user->department);
        $this->assertEquals('Developer', $user->position);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    /** @test */
    public function it_can_soft_delete_a_user()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $userId]);
        $this->assertTrue($user->trashed());
    }

    /** @test */
    public function it_can_restore_a_soft_deleted_user()
    {
        $user = User::factory()->create();
        $user->delete();

        $user->restore();

        $this->assertNotSoftDeleted('users', ['id' => $user->id]);
        $this->assertFalse($user->trashed());
    }

    /** @test */
    public function it_can_scope_to_active_users()
    {
        $activeUser = User::factory()->create();
        $deletedUser = User::factory()->create();
        $deletedUser->delete();

        $activeUsers = User::active()->get();

        $this->assertCount(1, $activeUsers);
        $this->assertTrue($activeUsers->contains($activeUser));
        $this->assertFalse($activeUsers->contains($deletedUser));
    }

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Administrator');

        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $this->assertTrue($admin->hasRole('Administrator'));
        $this->assertFalse($employee->hasRole('Administrator'));
    }

    /** @test */
    public function it_can_check_if_user_is_employee()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $customer = User::factory()->create();
        $customer->assignRole('Customer');

        $this->assertTrue($employee->hasRole('Employee'));
        $this->assertFalse($customer->hasRole('Employee'));
    }

    /** @test */
    public function it_can_check_if_user_is_customer()
    {
        $customer = User::factory()->create();
        $customer->assignRole('Customer');

        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $this->assertTrue($customer->hasRole('Customer'));
        $this->assertFalse($employee->hasRole('Customer'));
    }

    /** @test */
    public function it_can_get_user_full_name()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
        ]);

        $this->assertEquals('John Doe', $user->getFullNameAttribute());
    }

    /** @test */
    public function it_can_get_user_avatar_url()
    {
        $user = User::factory()->create();

        // Test default avatar
        $this->assertStringContains('gravatar.com', $user->getAvatarUrlAttribute());

        // Test custom avatar
        $user->avatar = 'custom-avatar.jpg';
        $this->assertEquals('custom-avatar.jpg', $user->getAvatarUrlAttribute());
    }

    /** @test */
    public function it_can_get_user_initials()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $this->assertEquals('JD', $user->getInitialsAttribute());

        $user->name = 'John';
        $this->assertEquals('J', $user->getInitialsAttribute());
    }

    /** @test */
    public function it_can_belong_to_companies()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $user->companies()->attach($company->id);

        $this->assertCount(1, $user->companies);
        $this->assertTrue($user->companies->contains($company));
    }

    /** @test */
    public function it_can_belong_to_projects()
    {
        $user = User::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $user->projects()->attach([$project1->id, $project2->id]);

        $this->assertCount(2, $user->projects);
        $this->assertTrue($user->projects->contains($project1));
        $this->assertTrue($user->projects->contains($project2));
    }

    /** @test */
    public function it_can_have_assigned_tickets()
    {
        $user = User::factory()->create();
        $ticket1 = Ticket::factory()->assignedTo($user)->create();
        $ticket2 = Ticket::factory()->assignedTo($user)->create();
        $ticket3 = Ticket::factory()->create(); // Not assigned to user

        $assignedTickets = $user->assignedTickets;

        $this->assertCount(2, $assignedTickets);
        $this->assertTrue($assignedTickets->contains($ticket1));
        $this->assertTrue($assignedTickets->contains($ticket2));
        $this->assertFalse($assignedTickets->contains($ticket3));
    }

    /** @test */
    public function it_can_have_created_tickets()
    {
        $user = User::factory()->create();
        $ticket1 = Ticket::factory()->createdBy($user)->create();
        $ticket2 = Ticket::factory()->createdBy($user)->create();
        $ticket3 = Ticket::factory()->create(); // Not created by user

        $createdTickets = $user->createdTickets;

        $this->assertCount(2, $createdTickets);
        $this->assertTrue($createdTickets->contains($ticket1));
        $this->assertTrue($createdTickets->contains($ticket2));
        $this->assertFalse($createdTickets->contains($ticket3));
    }

    /** @test */
    public function it_can_have_comments()
    {
        $user = User::factory()->create();
        $comment1 = Comment::factory()->create(['user_id' => $user->id]);
        $comment2 = Comment::factory()->create(['user_id' => $user->id]);

        $comments = $user->comments;

        $this->assertCount(2, $comments);
        $this->assertTrue($comments->contains($comment1));
        $this->assertTrue($comments->contains($comment2));
    }

    /** @test */
    public function it_can_have_favorite_projects()
    {
        $user = User::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        FavoriteProject::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project1->id,
        ]);
        FavoriteProject::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project2->id,
        ]);

        $favoriteProjects = $user->favoriteProjects;

        $this->assertCount(2, $favoriteProjects);
        $this->assertTrue($favoriteProjects->contains($project1));
        $this->assertTrue($favoriteProjects->contains($project2));
    }

    /** @test */
    public function it_can_check_if_project_is_favorite()
    {
        $user = User::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        FavoriteProject::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project1->id,
        ]);

        $this->assertTrue($user->isFavoriteProject($project1->id));
        $this->assertFalse($user->isFavoriteProject($project2->id));
    }

    /** @test */
    public function it_can_get_ticket_statistics()
    {
        $user = User::factory()->create();
        
        // Create assigned tickets
        Ticket::factory()->assignedTo($user)->open()->count(3)->create();
        Ticket::factory()->assignedTo($user)->inProgress()->count(2)->create();
        Ticket::factory()->assignedTo($user)->resolved()->count(1)->create();
        
        $stats = $user->getTicketStatistics();

        $this->assertEquals(6, $stats['total']);
        $this->assertEquals(3, $stats['open']);
        $this->assertEquals(2, $stats['in_progress']);
        $this->assertEquals(1, $stats['resolved']);
    }

    /** @test */
    public function it_can_search_users_by_name()
    {
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);
        $user3 = User::factory()->create(['name' => 'Bob Johnson']);

        $results = User::search('John')->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($user1));
        $this->assertFalse($results->contains($user2));
        $this->assertFalse($results->contains($user3));
    }

    /** @test */
    public function it_can_search_users_by_email()
    {
        $user1 = User::factory()->create(['email' => 'john@example.com']);
        $user2 = User::factory()->create(['email' => 'jane@example.com']);
        $user3 = User::factory()->create(['email' => 'bob@example.com']);

        $results = User::search('example.com')->get();

        $this->assertCount(3, $results);
        $this->assertTrue($results->contains($user1));
        $this->assertTrue($results->contains($user2));
        $this->assertTrue($results->contains($user3));
    }

    /** @test */
    public function it_can_get_active_projects_count()
    {
        $user = User::factory()->create();
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        $project3 = Project::factory()->inactive()->create();

        $user->projects()->attach([$project1->id, $project2->id, $project3->id]);

        $activeCount = $user->getActiveProjectsCount();

        $this->assertEquals(2, $activeCount);
    }
}
