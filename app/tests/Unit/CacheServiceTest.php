<?php

namespace Tests\Unit;

use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Project;

class CacheServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /** @test */
    public function it_can_cache_user_data()
    {
        $user = User::factory()->create();
        $userData = ['name' => 'John Doe', 'email' => 'john@example.com'];

        CacheService::cacheUserData($user->id, $userData);

        $cachedData = CacheService::getUserData($user->id);
        $this->assertEquals($userData, $cachedData);
    }

    /** @test */
    public function it_returns_null_for_nonexistent_user_data()
    {
        $result = CacheService::getUserData(999);

        $this->assertNull($result);
    }

    /** @test */
    public function it_can_invalidate_user_cache()
    {
        $user = User::factory()->create();
        $userData = ['name' => 'John Doe'];
        
        CacheService::cacheUserData($user->id, $userData);
        $this->assertNotNull(CacheService::getUserData($user->id));

        CacheService::invalidateUserCache($user->id);
        $this->assertNull(CacheService::getUserData($user->id));
    }

    /** @test */
    public function it_can_cache_ticket_statistics()
    {
        $stats = [
            'total' => 10,
            'open' => 5,
            'in_progress' => 3,
            'resolved' => 2,
        ];

        CacheService::cacheTicketStatistics($stats);

        $cachedStats = CacheService::getTicketStatistics();
        $this->assertEquals($stats, $cachedStats);
    }

    /** @test */
    public function it_can_cache_project_statistics()
    {
        $stats = [
            'total' => 5,
            'active' => 3,
            'completed' => 2,
            'total_tickets' => 25,
        ];

        CacheService::cacheProjectStatistics($stats);

        $cachedStats = CacheService::getProjectStatistics();
        $this->assertEquals($stats, $cachedStats);
    }

    /** @test */
    public function it_can_cache_system_configuration()
    {
        $config = [
            'app_name' => 'Help Desk',
            'max_file_size' => 10485760,
            'allowed_extensions' => ['jpg', 'png', 'pdf'],
        ];

        CacheService::cacheSystemConfiguration($config);

        $cachedConfig = CacheService::getSystemConfiguration();
        $this->assertEquals($config, $cachedConfig);
    }

    /** @test */
    public function it_can_cache_user_permissions()
    {
        $user = User::factory()->create();
        $permissions = ['view_tickets', 'create_tickets', 'edit_tickets'];

        CacheService::cacheUserPermissions($user->id, $permissions);

        $cachedPermissions = CacheService::getUserPermissions($user->id);
        $this->assertEquals($permissions, $cachedPermissions);
    }

    /** @test */
    public function it_can_cache_user_roles()
    {
        $user = User::factory()->create();
        $roles = ['Employee', 'Project Manager'];

        CacheService::cacheUserRoles($user->id, $roles);

        $cachedRoles = CacheService::getUserRoles($user->id);
        $this->assertEquals($roles, $cachedRoles);
    }

    /** @test */
    public function it_can_cache_project_data()
    {
        $project = Project::factory()->create();
        $projectData = [
            'name' => 'Test Project',
            'description' => 'Test Description',
            'is_active' => true,
        ];

        CacheService::cacheProjectData($project->id, $projectData);

        $cachedData = CacheService::getProjectData($project->id);
        $this->assertEquals($projectData, $cachedData);
    }

    /** @test */
    public function it_can_cache_ticket_data()
    {
        $ticket = Ticket::factory()->create();
        $ticketData = [
            'title' => 'Test Ticket',
            'status' => 'Open',
            'priority' => 'High',
        ];

        CacheService::cacheTicketData($ticket->id, $ticketData);

        $cachedData = CacheService::getTicketData($ticket->id);
        $this->assertEquals($ticketData, $cachedData);
    }

    /** @test */
    public function it_can_cache_search_results()
    {
        $query = 'test query';
        $results = [
            ['id' => 1, 'title' => 'Test Ticket 1'],
            ['id' => 2, 'title' => 'Test Ticket 2'],
        ];

        CacheService::cacheSearchResults($query, $results);

        $cachedResults = CacheService::getSearchResults($query);
        $this->assertEquals($results, $cachedResults);
    }

    /** @test */
    public function it_can_invalidate_all_cache()
    {
        // Cache some data
        CacheService::cacheTicketStatistics(['total' => 10]);
        CacheService::cacheProjectStatistics(['total' => 5]);
        CacheService::cacheSystemConfiguration(['app_name' => 'Test']);

        // Verify data is cached
        $this->assertNotNull(CacheService::getTicketStatistics());
        $this->assertNotNull(CacheService::getProjectStatistics());
        $this->assertNotNull(CacheService::getSystemConfiguration());

        // Invalidate all cache
        CacheService::invalidateAllCache();

        // Verify cache is cleared
        $this->assertNull(CacheService::getTicketStatistics());
        $this->assertNull(CacheService::getProjectStatistics());
        $this->assertNull(CacheService::getSystemConfiguration());
    }

    /** @test */
    public function it_can_invalidate_tagged_cache()
    {
        $tag = 'test_tag';
        $key = 'test_key';
        $data = ['test' => 'data'];

        // Cache data with tag
        Cache::tags([$tag])->put($key, $data, 3600);
        $this->assertEquals($data, Cache::tags([$tag])->get($key));

        // Invalidate tagged cache
        CacheService::invalidateTaggedCache($tag);

        // Verify cache is cleared
        $this->assertNull(Cache::tags([$tag])->get($key));
    }

    /** @test */
    public function it_can_get_cache_hits_and_misses()
    {
        // Simulate cache operations
        Cache::put('test_key', 'test_value', 3600);
        Cache::get('test_key'); // Hit
        Cache::get('nonexistent_key'); // Miss

        $stats = CacheService::getCacheStats();

        $this->assertArrayHasKey('hits', $stats);
        $this->assertArrayHasKey('misses', $stats);
        $this->assertArrayHasKey('hit_rate', $stats);
    }

    /** @test */
    public function it_can_warm_up_cache()
    {
        // Create some test data
        User::factory()->count(5)->create();
        Ticket::factory()->count(10)->create();
        Project::factory()->count(3)->create();

        // Warm up cache
        CacheService::warmUpCache();

        // Verify cache contains expected data
        $this->assertNotNull(CacheService::getTicketStatistics());
        $this->assertNotNull(CacheService::getProjectStatistics());
    }

    /** @test */
    public function it_can_cache_with_ttl()
    {
        $key = 'test_ttl_key';
        $value = 'test_ttl_value';
        $ttl = 60; // 1 minute

        CacheService::cacheWithTTL($key, $value, $ttl);

        $this->assertEquals($value, CacheService::getWithTTL($key));
    }

    /** @test */
    public function it_can_remember_data()
    {
        $key = 'test_remember_key';
        $callback = function () {
            return 'computed_value';
        };

        // First call should execute callback
        $result1 = CacheService::remember($key, $callback, 3600);
        $this->assertEquals('computed_value', $result1);

        // Second call should use cached value
        $result2 = CacheService::remember($key, function () {
            return 'should_not_be_called';
        }, 3600);
        $this->assertEquals('computed_value', $result2);
    }

    /** @test */
    public function it_can_handle_cache_failure_gracefully()
    {
        // This test simulates a cache failure scenario
        $result = CacheService::safeGet('nonexistent_key', 'default_value');
        
        $this->assertEquals('default_value', $result);
    }

    /** @test */
    public function it_can_cache_large_data_sets()
    {
        $largeDataSet = [];
        for ($i = 0; $i < 1000; $i++) {
            $largeDataSet[] = [
                'id' => $i,
                'name' => "Item {$i}",
                'description' => str_repeat('Lorem ipsum ', 10),
            ];
        }

        CacheService::cacheLargeDataSet('large_data', $largeDataSet);

        $retrievedData = CacheService::getLargeDataSet('large_data');
        $this->assertEquals(count($largeDataSet), count($retrievedData));
    }

    /** @test */
    public function it_can_increment_cache_counter()
    {
        $key = 'test_counter';

        // Start with 0
        CacheService::incrementCounter($key);
        $this->assertEquals(1, CacheService::getCounter($key));

        // Increment multiple times
        CacheService::incrementCounter($key, 5);
        $this->assertEquals(6, CacheService::getCounter($key));
    }

    /** @test */
    public function it_can_decrement_cache_counter()
    {
        $key = 'test_counter';

        // Start with 10
        CacheService::setCounter($key, 10);
        CacheService::decrementCounter($key);
        $this->assertEquals(9, CacheService::getCounter($key));

        // Decrement multiple times
        CacheService::decrementCounter($key, 3);
        $this->assertEquals(6, CacheService::getCounter($key));
    }

    /** @test */
    public function it_can_cache_frequently_accessed_data()
    {
        $user = User::factory()->create();
        $data = ['name' => 'John Doe', 'email' => 'john@example.com'];

        // Mark as frequently accessed
        CacheService::cacheFrequentlyAccessedData("user_{$user->id}", $data);

        // Should be cached longer than normal
        $cachedData = CacheService::getFrequentlyAccessedData("user_{$user->id}");
        $this->assertEquals($data, $cachedData);
    }

    /** @test */
    public function it_can_cache_with_compression()
    {
        $largeData = str_repeat('This is a large string that should be compressed. ', 100);
        $key = 'compressed_data';

        CacheService::cacheWithCompression($key, $largeData);

        $retrievedData = CacheService::getWithDecompression($key);
        $this->assertEquals($largeData, $retrievedData);
    }
}
