<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ITSPay - Smart Finance Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
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
                    <li class="nav-item active">
                        <a href="{{ url('/home') }}" class="nav-link"><i class="fas fa-home"></i><span>Homepage</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/goals') }}" class="nav-link"><i class="fas fa-bullseye"></i><span>Goals</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/dashboard') }}" class="nav-link"><i class="fas fa-chart-bar"></i><span>Dashboard</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/advisor') }}" class="nav-link"><i class="fas fa-robot"></i><span>AI Advisor</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/transactions') }}" class="nav-link"><i class="fas fa-exchange-alt"></i><span>Transaction</span></a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="main-content">
            <header class="header">
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search for something">
                    </div>
                </div>
                <div class="header-actions">
                    <div class="notification-icon"><i class="fas fa-bell"></i><span class="notification-badge">0</span></div>
                    <div class="user-profile">
                        <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                        <div class="user-info">
                            <span class="user-name">{{ $user->name }}</span>
                            <span class="user-id">{{ $user->student_id }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="dashboard">
                <div class="welcome-section">
                    <h1 class="welcome-title">Welcome, ITS Students!</h1>
                    <p class="welcome-subtitle">Smart finance for ITS students — save, plan, and reach your goals.</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card saving-total">
                        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="stat-content">
                            <h3 class="stat-label">Saving Total</h3>
                            <p class="stat-value">Rp {{ number_format($savingTotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="stat-card active-goals">
                        <div class="stat-icon"><i class="fas fa-smile"></i></div>
                        <div class="stat-content">
                            <h3 class="stat-label">Active Goals</h3>
                            <p class="stat-value">{{ $activeCount }}</p>
                        </div>
                    </div>
                    <div class="stat-card achievement">
                        <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                        <div class="stat-content">
                            <h3 class="stat-label">Achievement</h3>
                            <p class="stat-value">{{ $achievementPercent }}%</p>
                        </div>
                    </div>
                    <div class="stat-card bonus">
                        <div class="stat-icon"><i class="fas fa-star"></i></div>
                        <div class="stat-content">
                            <h3 class="stat-label">This Week's Bonus</h3>
                            <p class="stat-value">{{ $weeklyBonus }}</p>
                        </div>
                    </div>
                </div>

                <div class="goals-grid">
                    @foreach($activeGoals as $goal)
                        @php
                            $progress = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
                            $today = \Carbon\Carbon::today();
                            $daysLeft = $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->diffInDays($today, false) : null;
                        @endphp
                        <div class="goal-card">
                            <div class="goal-header">
                                <div class="goal-icon">
                                    <i class="fas fa-{{ $goal->category === 'Need' ? 'laptop' : ($goal->category === 'Want' ? 'plane' : 'shield-alt') }}"></i>
                                </div>
                                <div class="goal-info">
                                    <h3 class="goal-title">{{ $goal->title }}</h3>
                                    <div class="goal-badges">
                                        <span class="goal-type">{{ $goal->category }}</span>
                                        <span class="goal-priority {{ strtolower($goal->priority) }}">{{ strtoupper($goal->priority) }}</span>
                                    </div>
                                    
                                    <!-- DISINI BADGE NYA --> 
                                    @if (!is_null($daysLeft))
                                        @if ($daysLeft <= 0)
                                            <span class="badge badge-red">Overdue by {{ abs($daysLeft) }} days</span>
                                        @elseif ($daysLeft <= 7)
                                            <span class="badge badge-orange">Due in {{ $daysLeft }} days</span>
                                        @else
                                            <span class="badge badge-green">{{ $daysLeft }} days left</span>
                                        @endif
                                    @else
                                        <span class="badge badge-gray">No deadline</span>
                                    @endif
                                    
                                </div>
                            </div>

                            <div class="goal-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="goal-amount">
                                    <span class="current-amount">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                                    <span class="target-amount">From Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <p class="goal-deadline">
                                <strong>Deadline:</strong> {{ \Carbon\Carbon::parse($goal->deadline)->format('d M Y') }}
                            </p>
                            <button class="see-details-btn"
                                data-title="{{ $goal->title }}"
                                data-category="{{ $goal->category }}"
                                data-priority="{{ $goal->priority }}"
                                data-target="{{ $goal->target_amount }}"
                                data-current="{{ $goal->current_amount }}"
                                data-progress="{{ round($progress) }}"
                                data-deadline="{{ \Carbon\Carbon::parse($goal->deadline)->format('d M Y') }}">
                                See Details
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>

    <div id="goalDetailModal" class="goal-modal" style="display: none;">
        <div class="goal-modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modal-title"></h2>
            <p><strong>Category:</strong> <span id="modal-category"></span></p>
            <p><strong>Priority:</strong> <span id="modal-priority"></span></p>
            <p><strong>Target Amount:</strong> Rp <span id="modal-target"></span></p>
            <p><strong>Current Amount:</strong> Rp <span id="modal-current"></span></p>
            <p><strong>Progress:</strong> <span id="modal-progress"></span>%</p>
            <p><strong>Deadline:</strong> <span id="modal-deadline"></span></p>
        </div>
    </div>

    <script>
        document.querySelectorAll('.see-details-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('modal-title').textContent = this.dataset.title;
                document.getElementById('modal-category').textContent = this.dataset.category;
                document.getElementById('modal-priority').textContent = this.dataset.priority;
                document.getElementById('modal-target').textContent = Number(this.dataset.target).toLocaleString('id-ID');
                document.getElementById('modal-current').textContent = Number(this.dataset.current).toLocaleString('id-ID');
                document.getElementById('modal-progress').textContent = this.dataset.progress;
                document.getElementById('modal-deadline').textContent = this.dataset.deadline;
                document.getElementById('goalDetailModal').style.display = 'block';
            });
        });
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('goalDetailModal').style.display = 'none';
        });
        window.addEventListener('click', function(e) {
            if (e.target.id === 'goalDetailModal') {
                document.getElementById('goalDetailModal').style.display = 'none';
            }
        });
    </script>
</body>
</html>
