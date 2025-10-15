<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_monitoring_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/monitoring/login');
    }
}
