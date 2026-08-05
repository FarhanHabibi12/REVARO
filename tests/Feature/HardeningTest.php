<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class HardeningTest extends TestCase
{
    /**
     * Test Redis driver configurations in .env.
     */
    public function test_redis_drivers_configured()
    {
        $envContent = file_get_contents(base_path('.env'));
        $this->assertStringContainsString('REDIS_HOST', $envContent);
        $this->assertStringContainsString('REDIS_PORT', $envContent);
    }

    /**
     * Test database read/write replica configurations.
     */
    public function test_database_replicas_configured()
    {
        $mysqlConfig = config('database.connections.mysql');

        // Assert read replica setup
        $this->assertArrayHasKey('read', $mysqlConfig);
        $this->assertArrayHasKey('host', $mysqlConfig['read']);

        // Assert write primary setup
        $this->assertArrayHasKey('write', $mysqlConfig);
        $this->assertArrayHasKey('host', $mysqlConfig['write']);

        // Assert sticky connection is enabled to handle replica lag
        $this->assertTrue($mysqlConfig['sticky']);
    }

    /**
     * Test API general rate limiting (throttle:api) configuration.
     */
    public function test_api_rate_limiting_blocks_after_limit()
    {
        // Resolve the registered 'api' rate limiter callback
        $limiter = RateLimiter::limiter('api');
        $this->assertNotNull($limiter, 'The api rate limiter must be registered.');

        // Simulate a request
        $request = \Illuminate\Http\Request::create('/api/auth/me', 'GET');
        
        // Execute the limiter callback
        $limit = $limiter($request);

        // Assert the limiter returns a Limit object with 60 max attempts
        $this->assertInstanceOf(\Illuminate\Cache\RateLimiting\Limit::class, $limit);
        $this->assertEquals(60, $limit->maxAttempts);
    }
}
