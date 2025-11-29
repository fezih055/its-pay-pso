<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    // ============== LOGIN VIEW TESTS ==============

    public function test_login_page_displays(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    // ============== PROTECTED PAGES TESTS ==============

    public function test_guest_cannot_access_home_page(): void
    {
        $response = $this->get('/home');

        $response->assertRedirect('/');
    }

    public function test_guest_cannot_access_goals_page(): void
    {
        $response = $this->get('/goals');

        $response->assertRedirect('/');
    }

    public function test_guest_cannot_access_dashboard_page(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/');
    }

    public function test_guest_cannot_access_transactions_page(): void
    {
        $response = $this->get('/transactions');

        $response->assertRedirect('/');
    }
}
