<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Saving Goals - ITSPay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/goals.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
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
                    <li class="nav-item active">
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

            <div class="goals-page">
                <div class="page-header">
                    <h1 class="page-title">Manage Saving Goals</h1>
                    <p class="page-subtitle">Monitor and organize all your financial goals in one place.</p>
                </div>

                <!--HTML FILTERING FORMAT-->
                <div class="filter-section">
                    <form method="GET" action="{{ url('/goals') }}" class="filter-form">
                        <label for="start">Start Date:</label>
                        <input type="date" name="start" id="start" value="{{ request('start') }}">

                        <label for="end">End Date:</label>
                        <input type="date" name="end" id="end" value="{{ request('end') }}">

                        <button type="submit" class="btn-filter">Filter</button>
                        <!-- Tombol reset -->
                        <a href="{{ url('/goals') }}" class="btn-reset">Reset</a>
                    </form>
                </div>

                <div class="goals-management-grid">

                    @foreach($goals as $goal)
                    @php
                    $progress = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
                    @endphp

                    <div class="goal-management-card {{ strtolower($goal->category) }}">
                        <div class="goal-icon-large">
                            <i class="fas {{ $goal->category === 'Need' ? 'fa-laptop' : ($goal->category === 'Want' ? 'fa-plane' : 'fa-shield-alt') }}"></i>
                        </div>
                        <div class="goal-content">
                            <h3 class="goal-title">{{ $goal->title }}</h3>
                            <div class="goal-progress-section">
                                <div class="progress-bar-large">
                                    <div class="progress-fill-large" style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="goal-amounts">
                                    <span class="current-amount">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                                    <span class="target-amount">From Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- DEADLINE-->
                            <p class="goal-deadline">
                                <strong>Deadline:</strong>
                                {{ $goal->deadline ? \Carbon\Carbon::parse($goal->deadline)->format('d M Y') : '-' }}
                            </p>



                            <div class="goal-actions">
                                <form method="POST" action="{{ url('/goals/'.$goal->id.'/delete') }}">
                                    @csrf
                                    <button class="btn-delete" type="submit">Delete</button>
                                </form>
                                <form">
                                    <!-- <button class="btn-edit" type="submit">Edit Goal</button> -->
                                    <button class="btn-edit-goal btn-edit" data-goalid="{{ $goal->id }}"
                                        data-title="{{ $goal->title }}"
                                        data-category="{{ $goal->category }}"
                                        data-priority="{{ $goal->priority }}"
                                        data-target="{{ $goal->target_amount }}">
                                        Edit Goal
                                    </button>
                                    </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="add-goal-section">
                    <button class="btn-add-goal"><i class="fas fa-plus"></i> Add New Goal</button>
                </div>

                <div class="add-goal-form" id="goalForm" style="display: none;">
                    <form method="POST" action="{{ url('/goals') }}">
                        @csrf
                        <h3>Add New Goal</h3>

                        <label>Title</label>
                        <input type="text" name="title" required>

                        <label>Category</label>
                        <select name="category" id="category" required>
                            <option value="">-- Select Category --</option>
                            <option value="Need">Need</option>
                            <option value="Want">Want</option>
                            <option value="Saving">Saving</option>
                        </select>

                        <label>Priority</label>
                        <input type="text" name="priority" id="priority" readonly>

                        <label>Target Amount</label>
                        <input type="number" name="target_amount" required>

                        <label>Saving This Month</label>
                        <input type="number" name="saving_amount" value="0" required>

                        <!-- ADD DEADLINE BUAT GOALS-->
                        <label>Deadline</label>
                        <input type="date" name="deadline" required>


                        <button type="submit" class="btn-add-goal-submit">Save Goal</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- MULAI DARI SINI DEADLINE -->
    <div class="modal-overlay" id="editGoalModal">
        <div class="modal-content">
            <form method="POST" id="editGoalForm">
                @csrf
                <h2>Edit Goal</h2>

                <input type="hidden" name="id" id="edit-goal-id">

                <div class="form-group">
                    <label for="edit-title">Title:</label>
                    <input type="text" id="edit-title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="edit-category">Category:</label>
                    <select id="edit-category" name="category" required>
                        <option value="Need">Need</option>
                        <option value="Want">Want</option>
                        <option value="Saving">Saving</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit-priority">Priority:</label>
                    <select id="edit-priority" name="priority" required>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit-target">Target Amount:</label>
                    <input type="number" id="edit-target" name="target_amount" required>
                </div>

                <div class="form-group">
                    <label for="edit-deadline">Deadline:</label>
                    <input type="date" id="edit-deadline" name="deadline" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-save">Save Changes</button>
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Script asli buat deadline ya-->
    <!-- <div class="modal-overlay" id="editGoalModal">
        <div class="modal-content">
            <form method="POST" id="editGoalForm">
                @csrf
                <h2>Edit Goal</h2>

                <input type="hidden" name="id" id="edit-goal-id">

                <div class="form-group">
                    <label for="edit-title">Title:</label>
                    <input type="text" id="edit-title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="edit-category">Category:</label>
                    <select id="edit-category" name="category" required>
                        <option value="Need">Need</option>
                        <option value="Want">Want</option>
                        <option value="Saving">Saving</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit-priority">Priority:</label>
                    <select id="edit-priority" name="priority" required>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit-target">Target Amount:</label>
                    <input type="number" id="edit-target" name="target_amount" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-save">Save Changes</button>
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div> -->
</body>

<script>
    const toggleBtn = document.querySelector('.btn-add-goal');
    const form = document.getElementById('goalForm');
    const category = document.getElementById('category');
    const priority = document.getElementById('priority');

    toggleBtn.addEventListener('click', function() {
        if (form.style.display === 'none') {
            form.style.display = 'block';
            form.scrollIntoView({
                behavior: 'smooth'
            });
        } else {
            form.style.display = 'none';
        }
    });

    category.addEventListener('change', function() {
        let selected = this.value;
        let autoPriority = '';
        if (selected === 'Need') autoPriority = 'High';
        else if (selected === 'Want') autoPriority = 'Low';
        else if (selected === 'Saving') autoPriority = 'Medium';
        priority.value = autoPriority;
    });
</script>

<!-- <script original, apa yg beda sama bawah adalah > 
    const modal = document.getElementById('editGoalModal');
    const formEdit = document.getElementById('editGoalForm');

    document.querySelectorAll('.btn-edit-goal').forEach(button => {
        button.addEventListener('click', () => {
            const goalId = button.dataset.goalid;
            const title = button.dataset.title;
            const category = button.dataset.category;
            const priority = button.dataset.priority;
            const target = button.dataset.target;

            formEdit.action = `/goals/${goalId}/update`;
            document.getElementById('edit-goal-id').value = goalId;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-category').value = category;
            document.getElementById('edit-priority').value = priority;
            document.getElementById('edit-target').value = target;

            modal.style.display = 'flex';
        });
    });

    function closeModal() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script> -->

<script>
    const modal = document.getElementById('editGoalModal');
    const formEdit = document.getElementById('editGoalForm');

    document.querySelectorAll('.btn-edit-goal').forEach(button => {
        button.addEventListener('click', () => {
            const goalId = button.dataset.goalid;
            const title = button.dataset.title;
            const category = button.dataset.category;
            const priority = button.dataset.priority;
            const target = button.dataset.target;
            const deadline = button.dataset.deadline;

            formEdit.action = `/goals/${goalId}/update`;
            document.getElementById('edit-goal-id').value = goalId;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-category').value = category;
            document.getElementById('edit-priority').value = priority;
            document.getElementById('edit-target').value = target;
            document.getElementById('edit-deadline').value = deadline;

            modal.style.display = 'flex';
        });
    });

    function closeModal() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>


</html>