<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - ITSPay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Load CSS dari public --}}
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="app-name">ITSPay</h1>
            </div>
            <h2 class="app-subtitle">Smart Financial Management for ITS Students</h2>
            <div class="features-list">
                <div class="feature-item"><i class="fas fa-shield-alt"></i><span>Secure</span></div>
                <div class="feature-item"><i class="fas fa-robot"></i><span>AI-Powered</span></div>
                <div class="feature-item"><i class="fas fa-clock"></i><span>Real-time</span></div>
            </div>
        </div>

        <!-- Form -->
        <div class="login-form-container">
            <div class="login-card">
                <div class="form-header">
                    <h3>Welcome Back!</h3>
                    <p>Sign in to manage your finances</p>
                </div>

                @if(session('error'))
                    <div style="color: red; text-align:center; margin-bottom: 10px;">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="login-form">
                    @csrf

                    <div class="form-group">
                        <label for="student_id">Student ID (NRP)</label>
                        <div class="input-container">
                            <input type="text" id="student_id" name="student_id" placeholder="e.g. 5026231055" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-container">
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">Login</button>

                    <div class="forgot-password">
                        <a href="#" class="forgot-link">Forgot Password</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Toggle eye icon (animation only) --}}
    <script>
        const toggle = document.querySelector('.password-toggle');
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');

        toggle.addEventListener('click', function () {
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
