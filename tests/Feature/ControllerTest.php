<?php

namespace Tests\Feature;

use Tests\TestCase;

class ControllerTest extends TestCase
{
    public function test_login_page_view_renders(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_welcome_page_renders(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
