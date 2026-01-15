<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container-fluid">
        <!-- Left Side - Sidebar Toggle -->
        <div class="d-flex align-items-center">
            <button class="btn btn-link text-dark p-2" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <!-- Page Title (shown on mobile) -->
            @hasSection('header')
            <h5 class="mb-0 d-lg-none ms-2">@yield('header')</h5>
            @endif
        </div>

        <!-- Right Side -->
        <div class="d-flex align-items-center gap-3">
            
            <!-- Super Admin Badge -->
            @if(auth()->user()->isCentralUser())
            <span class="badge rounded-pill" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 0.7rem; padding: 0.4rem 0.8rem;">
                <i class="bi bi-shield-check me-1"></i>SUPER ADMIN
            </span>
            @endif

            <!-- Current Timer Badge -->
            @php
                $runningTimer = \App\Models\TimeEntry::where('user_id', auth()->id())->running()->first();
            @endphp
            @if($runningTimer)
            <div class="dropdown">
                <a class="btn btn-success btn-sm d-flex align-items-center gap-2 dropdown-toggle" 
                   href="#" id="timerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    <span class="fw-semibold" id="timerDisplay">
                        {{ gmdate('H:i:s', (now()->timestamp - $runningTimer->start_time->timestamp)) }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 320px;" aria-labelledby="timerDropdown">
                    <li class="px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted fw-semibold">ACTIVE TIMER</small>
                            <span class="badge bg-success"><i class="bi bi-play-fill"></i> Running</span>
                        </div>
                    </li>
                    <li class="px-3 py-3">
                        <div class="mb-2">
                            <small class="text-muted d-block">Project</small>
                            <a href="{{ route('projects.show', $runningTimer->project) }}" class="text-decoration-none fw-semibold">
                                {{ $runningTimer->project->name }}
                            </a>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Task</small>
                            <a href="{{ route('tasks.show', $runningTimer->task) }}" class="text-decoration-none fw-semibold">
                                {{ $runningTimer->task->name }}
                            </a>
                        </div>
                        @if($runningTimer->notes)
                        <div class="mb-0">
                            <small class="text-muted d-block">Notes</small>
                            <div class="small">{{ Str::limit($runningTimer->notes, 50) }}</div>
                        </div>
                        @endif
                    </li>
                    <li><hr class="dropdown-divider my-0"></li>
                    <li class="px-3 py-2">
                        <div class="d-flex gap-2">
                            <a href="{{ route('time-entries.show', $runningTimer) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <form action="{{ route('time-entries.stop', $runningTimer) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                    <i class="bi bi-stop-circle"></i> Stop
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
            @else
            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#quickStartTimerModal">
                <i class="bi bi-play-circle me-1"></i>Start Timer
            </button>
            @endif

            <!-- User Dropdown -->
            <div class="dropdown">
                <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" 
                   id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px;">
                            <i class="bi bi-person-fill text-primary"></i>
                        </div>
                        <div class="ms-2 d-none d-md-block">
                            <div class="fw-semibold text-dark">{{ Auth::user()->name }}</div>
                            <small class="text-muted">{{ ucfirst(Auth::user()->role ?? 'User') }}</small>
                        </div>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 250px;" aria-labelledby="userDropdown">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-semibold">{{ Auth::user()->name }}</div>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                        @if(!auth()->user()->isSuperAdmin() && auth()->user()->tenant)
                        <div class="mt-1">
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-building me-1"></i>{{ auth()->user()->tenant->name }}
                            </span>
                        </div>
                        @endif
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('settings.index', ['section' => 'profile']) }}">
                            <i class="bi bi-person me-2 text-primary"></i>My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('settings.index') }}">
                            <i class="bi bi-gear me-2 text-primary"></i>Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="#">
                            <i class="bi bi-question-circle me-2 text-primary"></i>Help & Support
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-2"></li>
                    <li>
                        <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Quick Start Timer Modal -->
<div class="modal fade" id="quickStartTimerModal" tabindex="-1" aria-labelledby="quickStartTimerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('time-entries.start') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="quickStartTimerModalLabel">
                        <i class="bi bi-play-circle text-success me-2"></i>Start Timer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quick_project_id" class="form-label">Project <span class="text-danger">*</span></label>
                        <select class="form-select" id="quick_project_id" name="project_id" required>
                            <option value="">Select Project</option>
                            @foreach(\App\Models\Project::active()->get() as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quick_task_id" class="form-label">Task <span class="text-danger">*</span></label>
                        <select class="form-select" id="quick_task_id" name="task_id" required>
                            <option value="">Select Task</option>
                            @foreach(\App\Models\Task::pending()->get() as $task)
                            <option value="{{ $task->id }}" data-project="{{ $task->project_id }}">{{ $task->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quick_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="quick_notes" name="notes" rows="2" placeholder="What are you working on?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-play-circle me-1"></i>Start Timer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Filter tasks by selected project in quick start modal
    document.addEventListener('DOMContentLoaded', function() {
        const projectSelect = document.getElementById('quick_project_id');
        const taskSelect = document.getElementById('quick_task_id');
        
        if (projectSelect && taskSelect) {
            projectSelect.addEventListener('change', function() {
                const projectId = this.value;
                const options = taskSelect.querySelectorAll('option[data-project]');
                
                taskSelect.value = '';
                
                options.forEach(option => {
                    if (!projectId || option.dataset.project === projectId) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }
    });
</script>

@if($runningTimer ?? false)
<script>
    // Update timer display every second
    document.addEventListener('DOMContentLoaded', function() {
        const startTime = new Date('{{ $runningTimer->start_time->toIso8601String() }}');
        const timerDisplay = document.getElementById('timerDisplay');
        
        function updateTimer() {
            const now = new Date();
            const diff = Math.floor((now - startTime) / 1000);
            const hours = Math.floor(diff / 3600).toString().padStart(2, '0');
            const minutes = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
            const seconds = (diff % 60).toString().padStart(2, '0');
            timerDisplay.textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
    });
</script>
@endif
