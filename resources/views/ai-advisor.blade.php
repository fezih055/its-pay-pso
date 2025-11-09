<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Financial Advisor - ITSPay</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ai-advisor.css') }}">
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
                    <li class="nav-item active">
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

            <!-- AI Advisor Content -->
            <div class="ai-advisor-page">
                <div class="page-header">
                    <h1 class="page-title">AI Financial Advisor</h1>
                    <p class="page-subtitle">Get smart insights, accurate predictions, and personalized recommendations to achieve your financial goals.</p>
                </div>

                <!-- AI Insights Grid -->
                <div class="ai-insights-grid">
                    <div class="insight-card achievement">
                        <div class="insight-header">
                            <h3>Best Achievement!</h3>
                            <i class="fas fa-trophy"></i>
                        </div>
                        <p>You've saved 25% more this month!</p>
                    </div>

                    <div class="insight-card warning">
                        <div class="insight-header">
                            <h3>Spending Alerts!</h3>
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <p>Your coffee spending is 45% higher than average.</p>
                    </div>

                    <div class="insight-card suggestion">
                        <div class="insight-header">
                            <h3>Investment Suggestion</h3>
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <p>Consider allocating 15% to mutual funds.</p>
                    </div>

                    <div class="insight-card target">
                        <div class="insight-header">
                            <h3>Target Achieved</h3>
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <p>The "Coffee Fund" goal will be reached 8 days earlier!</p>
                    </div>
                </div>

                <!-- AI Tools Section -->
                <div class="ai-tools-section">
                    <div class="ai-tool-card quick-help">
                        <div class="tool-header">
                            <i class="fas fa-question-circle"></i>
                            <h3>Quick Help</h3>
                        </div>
                    </div>

                    <div class="ai-tool-card forecast">
                        <div class="tool-header">
                            <i class="fas fa-crystal-ball"></i>
                            <h3>Next Month's Financial Forecast</h3>
                        </div>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="faq-section">
                    <div class="faq-item">
                        <div class="faq-question">
                            <span class="question-number">? 1.</span>
                            <span class="question-text">How do I start saving for a goal?</span>
                        </div>
                        <div class="faq-answer">
                            <p>To start saving, just tap "Add Goal" on the Goals page and fill in the required fields.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span class="question-number">? 2.</span>
                            <span class="question-text">What's the difference between Needs, Wants, and Savings?</span>
                        </div>
                        <div class="faq-answer">
                            <p>Needs are essential expenses like a new laptop for class. Wants are nice-to-haves, like trips or gadgets. Savings are for emergencies or long-term plans.</p>
                        </div>
                    </div>
                </div>

                <!-- Financial Forecast -->
                <div class="forecast-section">
                    @foreach($forecasts as $item)
                    <div class="forecast-card">
                        <h3>{{ $item['label'] }}</h3>
                        <div class="forecast-item">
                            <span class="forecast-label">Current: Rp {{ number_format($item['current'], 0, ',', '.') }}</span>
                            <span class="forecast-value">Forecast: Rp {{ number_format($item['forecast'], 0, ',', '.') }}</span>
                            @php
                            $diff = $item['forecast'] - $item['current'];
                            $percentage = round(($diff / $item['current']) * 100, 1);
                            $class = $diff >= 0 ? 'positive' : 'negative';
                            $sign = $diff >= 0 ? '+' : '';
                            @endphp
                            <span class="forecast-change {{ $class }}">{{ $sign }}{{ $percentage }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </main>
    </div>
</body>
<script>
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const item = question.closest('.faq-item');
            item.classList.toggle('open');
        });
    });
</script>

</html>