<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
    }

    protected function createTables(): void
    {
        if (!Schema::hasTable('goals')) {
            Schema::create('goals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('title');
                $table->string('category');
                $table->string('priority');
                $table->decimal('target_amount', 12, 2);
                $table->decimal('current_amount', 12, 2);
                $table->date('deadline')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('description');
                $table->enum('type', ['income', 'expense']);
                $table->string('category');
                $table->decimal('amount', 12, 2);
                $table->string('status')->default('completed');
                $table->timestamp('transaction_date')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Test dashboard page renders for authenticated user
     */
    public function test_dashboard_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    /**
     * Test dashboard displays user data
     */
    public function test_dashboard_displays_user_data(): void
    {
        $user = User::factory()->create(['name' => 'Test User']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('user');
        $this->assertEquals($user->id, $response->viewData('user')->id);
    }

    /**
     * Test dashboard displays zero savings when no goals
     */
    public function test_dashboard_displays_zero_savings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('savings', 0);
    }

    /**
     * Test dashboard displays goals achieved count
     */
    public function test_dashboard_displays_goals_achieved_count(): void
    {
        $user = User::factory()->create();

        // Create a goal that's not achieved
        \DB::table('goals')->insert([
            'user_id' => $user->id,
            'title' => 'Laptop Fund',
            'category' => 'Equipment',
            'priority' => 'High',
            'target_amount' => 5000000,
            'current_amount' => 3000000,
            'deadline' => now()->addMonth(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a goal that's achieved
        \DB::table('goals')->insert([
            'user_id' => $user->id,
            'title' => 'Emergency Fund',
            'category' => 'Savings',
            'priority' => 'High',
            'target_amount' => 1000000,
            'current_amount' => 1500000, // exceeds target
            'deadline' => now()->addMonth(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('goalsAchieved', 1);
    }

    /**
     * Test dashboard calculates total savings correctly
     */
    public function test_dashboard_calculates_total_savings(): void
    {
        $user = User::factory()->create();

        \DB::table('goals')->insert([
            [
                'user_id' => $user->id,
                'title' => 'Goal 1',
                'category' => 'Savings',
                'priority' => 'High',
                'target_amount' => 1000000,
                'current_amount' => 500000,
                'deadline' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Goal 2',
                'category' => 'Savings',
                'priority' => 'Low',
                'target_amount' => 2000000,
                'current_amount' => 750000,
                'deadline' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        // 500000 + 750000 = 1250000
        $response->assertViewHas('savings', 1250000);
    }

    /**
     * Test dashboard displays monthly income
     */
    public function test_dashboard_displays_monthly_income(): void
    {
        $user = User::factory()->create();

        // Add income transactions this month
        \DB::table('transactions')->insert([
            [
                'user_id' => $user->id,
                'description' => 'Salary',
                'type' => 'income',
                'category' => 'Salary',
                'amount' => 5000000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'description' => 'Freelance',
                'type' => 'income',
                'category' => 'Freelance',
                'amount' => 1000000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        // 5000000 + 1000000 = 6000000
        $response->assertViewHas('monthlySavings', 6000000);
    }

    /**
     * Test dashboard displays spending data by category
     */
    public function test_dashboard_displays_spending_data(): void
    {
        $user = User::factory()->create();

        \DB::table('transactions')->insert([
            [
                'user_id' => $user->id,
                'description' => 'Lunch',
                'type' => 'expense',
                'category' => 'F&B',
                'amount' => 150000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'description' => 'Dinner',
                'type' => 'expense',
                'category' => 'F&B',
                'amount' => 200000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'description' => 'Bus fare',
                'type' => 'expense',
                'category' => 'Transport',
                'amount' => 50000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $spendingData = $response->viewData('spendingData');
        $this->assertEquals(350000, $spendingData['F&B']);
        $this->assertEquals(50000, $spendingData['Transport']);
    }

    /**
     * Test dashboard displays insights
     */
    public function test_dashboard_displays_insights(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('insights');
        $insights = $response->viewData('insights');
        $this->assertCount(3, $insights);
        $this->assertArrayHasKey('title', $insights[0]);
        $this->assertArrayHasKey('text', $insights[0]);
    }

    /**
     * Test guest cannot access dashboard
     */
    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/');
    }

    /**
     * Test dashboard shows only current user's data
     */
    public function test_dashboard_shows_only_current_users_data(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create goals for both users
        \DB::table('goals')->insert([
            [
                'user_id' => $user1->id,
                'title' => 'User 1 Goal',
                'category' => 'Savings',
                'priority' => 'High',
                'target_amount' => 1000000,
                'current_amount' => 500000,
                'deadline' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user2->id,
                'title' => 'User 2 Goal',
                'category' => 'Savings',
                'priority' => 'High',
                'target_amount' => 2000000,
                'current_amount' => 1000000,
                'deadline' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user1)->get('/dashboard');

        $response->assertViewHas('savings', 500000);
    }

    /**
     * Test dashboard monthly income excludes other months
     */
    public function test_dashboard_monthly_income_only_current_month(): void
    {
        $user = User::factory()->create();

        // Add income for current month
        \DB::table('transactions')->insert([
            'user_id' => $user->id,
            'description' => 'This Month Income',
            'type' => 'income',
            'category' => 'Salary',
            'amount' => 5000000,
            'status' => 'completed',
            'transaction_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add income for previous month
        \DB::table('transactions')->insert([
            'user_id' => $user->id,
            'description' => 'Previous Month Income',
            'type' => 'income',
            'category' => 'Salary',
            'amount' => 5000000,
            'status' => 'completed',
            'transaction_date' => now()->subMonth(),
            'created_at' => now()->subMonth(),
            'updated_at' => now()->subMonth(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        // Should only count this month's income
        $response->assertViewHas('monthlySavings', 5000000);
    }
}
