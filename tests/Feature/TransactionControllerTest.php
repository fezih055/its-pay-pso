<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
    }

    protected function createTables(): void
    {
        if (! Schema::hasTable('transactions')) {
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
     * Test transaction index page renders for authenticated user
     */
    public function test_transaction_index_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/transactions');

        $response->assertStatus(200);
        $response->assertViewIs('transaction');
    }

    /**
     * Test transaction page displays user
     */
    public function test_transaction_page_displays_user(): void
    {
        $user = User::factory()->create(['name' => 'Test User']);

        $response = $this->actingAs($user)->get('/transactions');

        $response->assertViewHas('user');
        $this->assertEquals($user->id, $response->viewData('user')->id);
    }

    /**
     * Test transaction page displays empty transactions
     */
    public function test_transaction_page_displays_empty_transactions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/transactions');

        $response->assertViewHas('transactions');
        $transactions = $response->viewData('transactions');
        $this->assertCount(0, $transactions);
    }

    /**
     * Test transaction page displays user transactions
     */
    public function test_transaction_page_displays_user_transactions(): void
    {
        $user = User::factory()->create();

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
                'description' => 'Lunch',
                'type' => 'expense',
                'category' => 'F&B',
                'amount' => 50000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/transactions');

        $transactions = $response->viewData('transactions');
        $this->assertCount(2, $transactions);
    }

    /**
     * Test transaction page calculates total income
     */
    public function test_transaction_page_calculates_total_income(): void
    {
        $user = User::factory()->create();

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
            [
                'user_id' => $user->id,
                'description' => 'Expense',
                'type' => 'expense',
                'category' => 'F&B',
                'amount' => 100000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/transactions');

        // Only income transactions: 5000000 + 1000000 = 6000000
        $response->assertViewHas('totalIncome', 6000000);
    }

    /**
     * Test transaction page calculates total expense
     */
    public function test_transaction_page_calculates_total_expense(): void
    {
        $user = User::factory()->create();

        \DB::table('transactions')->insert([
            [
                'user_id' => $user->id,
                'description' => 'Lunch',
                'type' => 'expense',
                'category' => 'F&B',
                'amount' => 50000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'description' => 'Transport',
                'type' => 'expense',
                'category' => 'Transport',
                'amount' => 75000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'description' => 'Income',
                'type' => 'income',
                'category' => 'Salary',
                'amount' => 5000000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/transactions');

        // Only expense transactions: 50000 + 75000 = 125000
        $response->assertViewHas('totalExpense', 125000);
    }

    /**
     * Test transaction page displays total transaction count
     */
    public function test_transaction_page_displays_total_transaction_count(): void
    {
        $user = User::factory()->create();

        \DB::table('transactions')->insert([
            [
                'user_id' => $user->id,
                'description' => 'Transaction 1',
                'type' => 'income',
                'category' => 'Salary',
                'amount' => 1000000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'description' => 'Transaction 2',
                'type' => 'expense',
                'category' => 'F&B',
                'amount' => 100000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'description' => 'Transaction 3',
                'type' => 'expense',
                'category' => 'Transport',
                'amount' => 50000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/transactions');

        $response->assertViewHas('totalTransaction', 3);
    }

    /**
     * Test guest cannot access transaction page
     */
    public function test_guest_cannot_access_transaction_page(): void
    {
        $response = $this->get('/transactions');

        $response->assertRedirect('/');
    }

    /**
     * Test transaction page shows only current user's transactions
     */
    public function test_transaction_page_shows_only_current_users_transactions(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create transactions for both users
        \DB::table('transactions')->insert([
            [
                'user_id' => $user1->id,
                'description' => 'User 1 Transaction',
                'type' => 'income',
                'category' => 'Salary',
                'amount' => 5000000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user2->id,
                'description' => 'User 2 Transaction',
                'type' => 'income',
                'category' => 'Salary',
                'amount' => 3000000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user1)->get('/transactions');

        $transactions = $response->viewData('transactions');
        $this->assertCount(1, $transactions);
        $this->assertEquals('User 1 Transaction', $transactions[0]->description);
    }

    /**
     * Test transaction page transactions ordered by latest first
     */
    public function test_transaction_page_transactions_ordered_by_latest(): void
    {
        $user = User::factory()->create();

        // Create transactions with different dates
        \DB::table('transactions')->insert([
            [
                'user_id' => $user->id,
                'description' => 'Oldest Transaction',
                'type' => 'income',
                'category' => 'Salary',
                'amount' => 1000000,
                'status' => 'completed',
                'transaction_date' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'user_id' => $user->id,
                'description' => 'Newest Transaction',
                'type' => 'expense',
                'category' => 'F&B',
                'amount' => 100000,
                'status' => 'completed',
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->get('/transactions');

        $transactions = $response->viewData('transactions');
        $this->assertEquals('Newest Transaction', $transactions[0]->description);
        $this->assertEquals('Oldest Transaction', $transactions[1]->description);
    }

    /**
     * Test store transaction creates new transaction
     */
    public function test_store_transaction_creates_new_transaction(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/transactions', [
            'description' => 'New Expense',
            'type' => 'expense',
            'category' => 'F&B',
            'amount' => 150000,
        ]);

        $response->assertRedirect('/transactions');

        $transaction = \DB::table('transactions')
            ->where('user_id', $user->id)
            ->where('description', 'New Expense')
            ->first();

        $this->assertNotNull($transaction);
        $this->assertEquals('expense', $transaction->type);
        $this->assertEquals('F&B', $transaction->category);
        $this->assertEquals(150000, $transaction->amount);
    }

    /**
     * Test store transaction uses authenticated user
     */
    public function test_store_transaction_uses_authenticated_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1)->post('/transactions', [
            'description' => 'User 1 Transaction',
            'type' => 'income',
            'category' => 'Salary',
            'amount' => 5000000,
        ]);

        $transaction = \DB::table('transactions')
            ->where('description', 'User 1 Transaction')
            ->first();

        $this->assertEquals($user1->id, $transaction->user_id);
        $this->assertNotEquals($user2->id, $transaction->user_id);
    }
}
