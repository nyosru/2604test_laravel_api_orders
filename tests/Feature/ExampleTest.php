<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_health_route_returns_expected_payload(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertJson([
                'app' => 'Laravel 12 Orders API',
                'status' => 'ok',
            ]);
    }
}
