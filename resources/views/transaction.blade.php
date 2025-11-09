<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - ITSPay</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/transaction.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span class="logo-text">ITSPay</span>
                    <p class="logo-subtitle">Smart Finance Platform</p>
                </div>
            </div>
            <div class="nav-menu">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ url('/home') }}" class="nav-link">
                            <i class="fas fa-home"></i>
                            <span>Homepage</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/goals') }}" class="nav-link">
                            <i class="fas fa-bullseye"></i>
                            <span>Goals</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/dashboard') }}" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/advisor') }}" class="nav-link">
                            <i class="fas fa-robot"></i>
                            <span>AI Advisor</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="{{ url('/transactions') }}" class="nav-link">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Transaction</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search for something">
                    </div>
                </div>

                <div class="header-actions">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">0</span>
                    </div>

                    <div class="user-profile">
                        <div class="user-avatar">JS</div>
                        <div class="user-info">
                            <span class="user-name">John Student</span>
                            <span class="user-id">502823055</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Transaction Content -->
            <div class="transaction-page">
                <div class="page-header">
                    <h1 class="page-title">Transaction History</h1>
                    <p class="page-subtitle">Monitor all your financial transactions with search and filter features.</p>
                </div>

                <!-- Transaction Summary Cards -->
                <div class="transaction-summary-grid">
                    <div class="summary-card income">
                        <div class="summary-content">
                            <h3 class="summary-title">Total Income</h3>
                            <p class="summary-value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                            <p class="summary-period">This month</p>
                        </div>
                    </div>

                    <div class="summary-card expenses">
                        <div class="summary-content">
                            <h3 class="summary-title">Total Expenses</h3>
                            <p class="summary-value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                            <p class="summary-period">This month</p>
                        </div>
                    </div>

                    <div class="summary-card transactions">
                        <div class="summary-content">
                            <h3 class="summary-title">Transaction</h3>
                            <p class="summary-value">{{ $totalTransaction }}</p>
                            <p class="summary-period">This month</p>
                        </div>
                    </div>

                    <div class="summary-card average">
                        <div class="summary-content">
                            <h3 class="summary-title">Average daily</h3>
                            <p class="summary-value">Rp {{ number_format($averageDaily, 0, ',', '.') }}</p>
                            <p class="summary-period">This month</p>
                        </div>
                    </div>
                </div>

                <!-- Transaction List Container -->
                <div class="transaction-container">
                    <div class="transaction-header">
                        <h2>Transaction List</h2>
                    </div>

                    <div class="transaction-list">
                        <!-- transaction item -->
                        @foreach($transactions as $t)
                        <div class="transaction-item {{ $t->type === 'income' ? 'income-item' : 'expense-item' }}">
                            <div class="transaction-icon">
                                <i class="fas {{ $t->type === 'income' ? 'fa-plus' : 'fa-minus' }}"></i>
                            </div>
                            <div class="transaction-details">
                                <h4 class="transaction-title">{{ $t->description }}</h4>
                                <div class="transaction-badges">
                                    <span class="transaction-badge default }}">{{ $t->category }}</span>
                                    <span class="transaction-badge completed">{{ $t->status }}</span>
                                </div>
                            </div>
                            <div class="transaction-amount {{ $t->type === 'income' ? 'positive' : 'negative' }}">
                                {{ $t->type === 'income' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Add Transaction Button -->
                    <div class="add-transaction-section">
                        <button class="btn-add-transaction" onclick="toggleTransactionForm()">
                            <i class="fas fa-plus"></i>
                            Add Transaction
                        </button>
                    </div>

                    <!-- Hidden Form -->
                    <div id="transaction-form" style="display: none; margin-top: 20px;">
                        <form method="POST" action="{{ url('/transactions') }}">
                            @csrf
                            <h3>Add Transaction</h3>

                            <label>Description</label>
                            <input type="text" name="description" required>

                            <label>Category</label>
                            <input type="text" name="category" required>

                            <label>Type</label>
                            <select name="type" required>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>

                            <label>Amount</label>
                            <input type="number" name="amount" required>

                            <label>Status</label>
                            <select name="status" required>
                                <option value="Completed">Completed</option>
                                <option value="Pending">Pending</option>
                            </select>

                            <button type="submit" class="btn-add-goal-submit">Save Transaction</button>
                        </form>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
<script>
    function toggleTransactionForm() {
        const form = document.getElementById('transaction-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
</script>

</html>