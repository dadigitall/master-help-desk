<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\Chat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles and permissions
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    /** @test */
    public function dashboard_loads_within_acceptable_time()
    {
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $user->givePermissionTo('dashboard.view');

        $companies = Company::factory()->count(5)->create();
        $projects = Project::factory()->count(20)->create();
        
        // Create a significant number of tickets
        Ticket::factory()->count(100)->create();

        $startTime = microtime(true);

        $response = $this->actingAs($user)
            ->get('/dashboard');

        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $response->assertStatus(200);
        $this->assertLessThan(2000, $loadTime, 'Dashboard should load within 2 seconds');
    }

    /** @test */
    public function ticket_index_loads_within_acceptable_time()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('tickets.view');

        $company = Company::factory()->create();
        $user->companies()->attach($company->id);

        $projects = Project::factory()->count(10)->create(['company_id' => $company->id]);
        
        // Create many tickets to test pagination performance
        Ticket::factory()->count(500)->create([
            'project_id' => $projects->random()->id,
        ]);

        $startTime = microtime(true);

        $response = $this->actingAs($user)
            ->get('/tickets');

        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(1500, $loadTime, 'Ticket index should load within 1.5 seconds');
    }

    /** @test */
    public function database_queries_are_optimized_for_ticket_index()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('tickets.view');

        $company = Company::factory()->create();
        $user->companies()->attach($company->id);

        $projects = Project::factory()->count(5)->create(['company_id' => $company->id]);
        Ticket::factory()->count(50)->create([
            'project_id' => $projects->random()->id,
        ]);

        // Enable query logging
        DB::enableQueryLog();

        $response = $this->actingAs($user)
            ->get('/tickets');

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $response->assertStatus(200);
        $this->assertLessThan(10, count($queries), 'Ticket index should use less than 10 database queries');
    }

    /** @test */
    public function cache_improves_dashboard_performance()
    {
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $user->givePermissionTo('dashboard.view');

        $companies = Company::factory()->count(5)->create();
        $projects = Project::factory()->count(20)->create();
        Ticket::factory()->count(100)->create();

        // Clear cache
        Cache::flush();

        // First load - no cache
        $startTime = microtime(true);
        $response1 = $this->actingAs($user)->get('/dashboard');
        $firstLoadTime = (microtime(true) - $startTime) * 1000;

        // Second load - with cache
        $startTime = microtime(true);
        $response2 = $this->actingAs($user)->get('/dashboard');
        $secondLoadTime = (microtime(true) - $startTime) * 1000;

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Cached version should be significantly faster
        $this->assertLessThan($firstLoadTime, $secondLoadTime, 'Cached dashboard should load faster');
    }

    /** @test */
    public function large_number_of_tickets_doesnt_degrade_performance()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('tickets.view');

        $company = Company::factory()->create();
        $user->companies()->attach($company->id);

        $projects = Project::factory()->count(10)->create(['company_id' => $company->id]);
        
        // Test with increasing numbers of tickets
        $ticketCounts = [100, 500, 1000];
        $maxAcceptableTime = 2000; // 2 seconds

        foreach ($ticketCounts as $count) {
            // Clear previous tickets
            Ticket::query()->delete();
            
            // Create tickets
            Ticket::factory()->count($count)->create([
                'project_id' => $projects->random()->id,
            ]);

            $startTime = microtime(true);
            $response = $this->actingAs($user)->get('/tickets');
            $loadTime = (microtime(true) - $startTime) * 1000;

            $response->assertStatus(200);
            $this->assertLessThan($maxAcceptableTime, $loadTime, 
                "Ticket index with {$count} tickets should load within {$maxAcceptableTime}ms");
        }
    }

    /** @test */
    public function concurrent_requests_handle_efficiently()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('tickets.view');

        $company = Company::factory()->create();
        $user->companies()->attach($company->id);

        $projects = Project::factory()->count(5)->create(['company_id' => $company->id]);
        Ticket::factory()->count(100)->create([
            'project_id' => $projects->random()->id,
        ]);

        $startTime = microtime(true);
        $maxConcurrentTime = 5000; // 5 seconds for all requests

        // Simulate concurrent requests
        $promises = [];
        for ($i = 0; $i < 10; $i++) {
            $promises[] = $this->actingAs($user)->get('/tickets');
        }

        // Wait for all requests to complete
        foreach ($promises as $response) {
            $response->assertStatus(200);
        }

        $totalTime = (microtime(true) - $startTime) * 1000;

        $this->assertLessThan($maxConcurrentTime, $totalTime, 
            '10 concurrent requests should complete within 5 seconds');
    }

    /** @test */
    public function memory_usage_remains_reasonable()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('tickets.view');

        $company = Company::factory()->create();
        $user->companies()->attach($company->id);

        $projects = Project::factory()->count(10)->create(['company_id' => $company->id]);
        Ticket::factory()->count(500)->create([
            'project_id' => $projects->random()->id,
        ]);

        $initialMemory = memory_get_usage(true);

        $response = $this->actingAs($user)
            ->get('/tickets');

        $finalMemory = memory_get_usage(true);
        $memoryUsed = $finalMemory - $initialMemory;

        $response->assertStatus(200);
        
        // Memory usage should be reasonable (less than 50MB)
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed, 
            'Memory usage should remain below 50MB');
    }

    /** @test */
    public function search_performance_is_acceptable()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('tickets.view');

        $company = Company::factory()->create();
        $user->companies()->attach($company->id);

        $projects = Project::factory()->count(5)->create(['company_id' => $company->id]);
        
        // Create tickets with searchable content
        Ticket::factory()->count(200)->create([
            'project_id' => $projects->random()->id,
            'title' => 'Test ticket with searchable content',
            'description' => 'This is a description that should be searchable',
        ]);

        $startTime = microtime(true);

        $response = $this->actingAs($user)
            ->get('/tickets?search=searchable');

        $endTime = microtime(true);
        $searchTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(1000, $searchTime, 'Search should complete within 1 second');
    }

    /** @test */
    public function export_performance_is_acceptable()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['tickets.view', 'tickets.export']);

        $company = Company::factory()->create();
        $user->companies()->attach($company->id);

        $projects = Project::factory()->count(5)->create(['company_id' => $company->id]);
        Ticket::factory()->count(1000)->create([
            'project_id' => $projects->random()->id,
        ]);

        $startTime = microtime(true);

        $response = $this->actingAs($user)
            ->get('/tickets/export');

        $endTime = microtime(true);
        $exportTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(5000, $exportTime, 'Export should complete within 5 seconds');
    }

    /** @test */
    public function dashboard_charts_load_efficiently()
    {
        $user = User::factory()->create();
        $user->assignRole('Administrator');
        $user->givePermissionTo('dashboard.stats');

        $companies = Company::factory()->count(5)->create();
        $projects = Project::factory()->count(20)->create();
        Ticket::factory()->count(200)->create();

        // Test dashboard charts component specifically
        $startTime = microtime(true);

        $response = $this->actingAs($user)
            ->get('/dashboard');

        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $response->assertSee('Advanced Analytics');
        $this->assertLessThan(3000, $loadTime, 'Dashboard with charts should load within 3 seconds');
    }

    /** @test */
    public function database_index_performance()
    {
        // Create indexes on commonly queried fields
        $this->artisan('migrate');

        $user = User::factory()->create();
        $user->givePermissionTo('tickets.view');

        $company = Company::factory()->create();
        $user->companies()->attach($company->id);

        $projects = Project::factory()->count(10)->create(['company_id' => $company->id]);
        Ticket::factory()->count(1000)->create([
            'project_id' => $projects->random()->id,
        ]);

        // Test specific filtered queries
        $filterTests = [
            'status=open',
            'priority=high',
            'assigned_to=' . $user->id,
            'project_id=' . $projects->first()->id,
        ];

        foreach ($filterTests as $filter) {
            $startTime = microtime(true);

            $response = $this->actingAs($user)
                ->get('/tickets?' . $filter);

            $endTime = microtime(true);
            $loadTime = ($endTime - $startTime) * 1000;

            $response->assertStatus(200);
            $this->assertLessThan(1000, $loadTime, 
                "Filtered query '{$filter}' should load within 1 second");
        }
    }

    /** @test */
    public function soft_delete_performance()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('tickets.view');

        $company = Company::factory()->create();
        $user->companies()->attach($company->id);

        $projects = Project::factory()->count(5)->create(['company_id' => $company->id]);
        
        // Create tickets and soft delete some
        $tickets = Ticket::factory()->count(200)->create([
            'project_id' => $projects->random()->id,
        ]);

        // Soft delete half of the tickets
        $tickets->take(100)->each->delete();

        $startTime = microtime(true);

        $response = $this->actingAs($user)
            ->get('/tickets');

        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(1500, $loadTime, 
            'Query with soft deletes should load within 1.5 seconds');
    }
}
