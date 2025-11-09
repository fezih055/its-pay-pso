<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard - ITSPay</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
                    <li class="nav-item active">
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
                    <li class="nav-item">
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
                        <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                        <div class="user-info">
                            <span class="user-name">{{ $user->name }}</span>
                            <span class="user-id">{{ $user->student_id }}</span>
                        </div>

                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-page">
                <div class="page-header">
                    <h1 class="page-title">Financial Dashboard</h1>
                    <p class="page-subtitle">Monitor your financial progress in real-time with insightful analytics.</p>
                </div>

                <!-- Financial Stats Grid -->
                <div class="financial-stats-grid">
                    <div class="financial-stat-card blue">
                        <div class="stat-header">
                            <h3 class="stat-title">Total Savings</h3>
                            <i class="fas fa-piggy-bank stat-icon"></i>
                        </div>
                        <div class="stat-value">Rp {{ number_format($savings, 0, ',', '.') }}</div>
                    </div>

                    <div class="financial-stat-card green">
                        <div class="stat-header">
                            <h3 class="stat-title">Goals Achieved</h3>
                            <i class="fas fa-trophy stat-icon"></i>
                        </div>
                        <div class="stat-value">{{ $goalsAchieved }}</div>
                        <div class="stat-subtitle">Targets reached</div>
                    </div>

                    <div class="financial-stat-card purple">
                        <div class="stat-header">
                            <h3 class="stat-title">Investment ROI</h3>
                            <i class="fas fa-chart-line stat-icon"></i>
                        </div>
                        <div class="stat-details">
                            <div class="investment-item">
                                <span>Mutual fund:</span>
                                <span class="positive">+12.5%</span>
                            </div>
                            <div class="investment-item">
                                <span>Deposita:</span>
                                <span class="positive">+6.8%</span>
                            </div>
                            <div class="investment-item">
                                <span>Crypto:</span>
                                <span class="positive">+23.3%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Section -->
                <div class="analytics-section">
                    <!-- Monthly Spending Analysis -->
                    <div class="analytics-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-chart-bar"></i>
                                <h3>Monthly Spending Analysis</h3>
                            </div>
                        </div>
                        <div class="spending-analysis">
                            @foreach($spendingData as $category => $amount)
                            @php
                            $colorClass = strtolower(preg_replace('/\s+/', '-', $category));
                            $formattedAmount = number_format($amount, 0, ',', '.');
                            @endphp

                            <div class="spending-item">
                                <div class="spending-label">
                                    <span class="category-indicator {{ $colorClass }}"></span>
                                    <span>{{ $category }}</span>
                                </div>
                                <div class="spending-bar">
                                    <div class="spending-progress {{ $colorClass }}"
                                        style="width: {{ min(100, round(($amount / array_sum($spendingData)) * 100)) }}%">
                                    </div>
                                </div>
                                <div class="spending-amount">
                                    Rp {{ $formattedAmount }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Smart Financial Insights -->
                    <div class="analytics-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-lightbulb"></i>
                                <h3>Smart Financial Insights</h3>
                            </div>
                        </div>
                        <div class="insights-container">
                            @foreach($insights as $insight)
                            <div class="insight-item {{ $insight['class'] }}">
                                <h4>{{ $insight['title'] }}</h4>
                                <p>{{ $insight['text'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
        </main>
    </div>
</body>

</html>