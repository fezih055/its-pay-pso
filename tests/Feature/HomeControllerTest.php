<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create goals table for testing
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
    }

    /**
     * Test home page renders for authenticated user
     */
    public function test_home_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
        ]);

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /**
     * Test home page displays user data
     */
    public function test_home_page_displays_user_data(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
        ]);

        $response = $this->actingAs($user)->get('/home');

        $response->assertViewHas('user');
        $this->assertEquals($user->id, $response->viewData('user')->id);
    }

    /**
     * Test home page displays active goals count (zero when no goals)
     */
    public function test_home_page_displays_active_count_zero(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
        $response->assertViewHas('activeCount', 0);
    }

    /**
     * Test home page displays saving total (zero when no goals)
     */
    public function test_home_page_displays_saving_total_zero(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
        $response->assertViewHas('savingTotal', 0);
    }

    /**
     * Test home page displays achievement percent (zero when no goals)
     */
    public function test_home_page_displays_achievement_percent_zero(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
        $response->assertViewHas('achievementPercent', 0);
    }

    /**
     * Test home page displays active goals list
     */
    public function test_home_page_displays_active_goals(): void
    {
        $user = User::factory()->create();

        // Create some goals in database
        \DB::table('goals')->insert([
            [
                'user_id' => $user->id,
                'title' => 'Emergency Fund',
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
                'title' => 'Laptop Fund',
                'category' => 'Equipment',
                'priority' => 'Medium',
                'target_amount' => 5000000,
                'current_amount' => 2000000,
                'deadline' => now()->addMonths(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/home');

        $response->assertViewHas('activeGoals');
        $activeGoals = $response->viewData('activeGoals');
        
        $this->assertCount(2, $activeGoals);
        $this->assertEquals('Emergency Fund', $activeGoals[0]->title);
        $this->assertEquals('Laptop Fund', $activeGoals[1]->title);
    }

    /**
     * Test home page calculates saving total correctly
     */
    public function test_home_page_calculates_saving_total(): void
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
                'current_amount' => 300000,
                'deadline' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/home');

        // 500000 + 300000 = 800000
        $response->assertViewHas('savingTotal', 800000);
    }

    /**
     * Test home page calculates achievement percent correctly
     */
    public function test_home_page_calculates_achievement_percent(): void
    {
        $user = User::factory()->create();

        \DB::table('goals')->insert([
            [
                'user_id' => $user->id,
                'title' => 'Goal 1',
                'category' => 'Savings',
                'priority' => 'High',
                'target_amount' => 1000000,
                'current_amount' => 500000, // 50%
                'deadline' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Goal 2',
                'category' => 'Savings',
                'priority' => 'Low',
                'target_amount' => 1000000,
                'current_amount' => 800000, // 80%
                'deadline' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/home');

        // (50% + 80%) / 2 = 65%
        $response->assertViewHas('achievementPercent', 65);
    }

    /**
     * Test guest cannot access home page
     */
    public function test_guest_cannot_access_home_page(): void
    {
        $response = $this->get('/home');

        $response->assertRedirect('/');
    }

    /**
     * Test home page displays weekly bonus
     */
    public function test_home_page_displays_weekly_bonus(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
        $response->assertViewHas('weeklyBonus', '+15%');
    }

    /**
     * Test home page shows only current user's goals
     */
    public function test_home_page_shows_only_current_users_goals(): void
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

        $response = $this->actingAs($user1)->get('/home');

        $activeGoals = $response->viewData('activeGoals');
        $this->assertCount(1, $activeGoals);
        $this->assertEquals('User 1 Goal', $activeGoals[0]->title);
    }

    /**
     * Test home page goals ordered by deadline
     */
    public function test_home_page_goals_ordered_by_deadline(): void
    {
        $user = User::factory()->create();

        \DB::table('goals')->insert([
            [
                'user_id' => $user->id,
                'title' => 'Goal Due Later',
                'category' => 'Savings',
                'priority' => 'Low',
                'target_amount' => 1000000,
                'current_amount' => 500000,
                'deadline' => now()->addMonths(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'title' => 'Goal Due Soon',
                'category' => 'Savings',
                'priority' => 'High',
                'target_amount' => 1000000,
                'current_amount' => 500000,
                'deadline' => now()->addDays(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/home');

        $activeGoals = $response->viewData('activeGoals');
        $this->assertEquals('Goal Due Soon', $activeGoals[0]->title);
        $this->assertEquals('Goal Due Later', $activeGoals[1]->title);
    }
}
