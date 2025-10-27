<aside class="app-sidebar d-flex flex-column" style="width: 280px;">
    <div class="p-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                 style="width: 40px; height: 40px;">
                <i class="bi bi-person text-white"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">{{ Auth::user()->name }}</div>
                <div class="text-muted small">{{ Auth::user()->email }}</div>
            </div>
        </div>
    </div>

    <nav class="flex-grow-1 p-3">
        <ul class="nav nav-modern flex-column">
            <!-- Dashboard -->
            <li class="nav-item mb-1">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                   href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <!-- Projects -->
            <li class="nav-item mb-1">
                <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" 
                   href="{{ route('projects.index') }}">
                    <i class="bi bi-folder"></i>
                    Projetos
                    @php
                        try {
                            $ownedCount = Auth::user()->projects()->count();
                            $memberCount = Auth::user()->memberProjects()->count();
                            $projectCount = $ownedCount + $memberCount;
                        } catch (Exception $e) {
                            $projectCount = 0;
                        }
                    @endphp
                    @if($projectCount > 0)
                        <span class="badge badge-modern badge-secondary ms-auto">{{ $projectCount }}</span>
                    @endif
                </a>
            </li>

            <!-- Board/Kanban -->
            <li class="nav-item mb-1">
                <a class="nav-link {{ request()->routeIs('board.*') ? 'active' : '' }}" 
                   href="{{ route('board.index') ?? '#' }}">
                    <i class="bi bi-kanban"></i>
                    Board Kanban
                </a>
            </li>

            <!-- Separator -->
            <li class="nav-divider my-3">
                <hr class="text-muted">
            </li>

            <!-- Recent Projects -->
            <li class="nav-header mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Projetos Recentes</span>
            </li>

            @php
                $recentProjects = \App\Models\Project::where('owner_id', Auth::id())
                    ->orWhereHas('users', function($query) {
                        $query->where('user_id', Auth::id());
                    })
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp

            @forelse($recentProjects as $project)
                <li class="nav-item mb-1">
                    <a class="nav-link py-2" href="{{ route('projects.show', $project) }}">
                        <div class="d-flex align-items-center">
                            <div class="bg-{{ ['primary', 'success', 'warning', 'info', 'secondary'][array_rand(['primary', 'success', 'warning', 'info', 'secondary'])] }} rounded-circle me-2" 
                                 style="width: 8px; height: 8px;"></div>
                            <span class="text-truncate">{{ $project->name }}</span>
                        </div>
                    </a>
                </li>
            @empty
                <li class="nav-item">
                    <div class="text-muted small px-3">Nenhum projeto encontrado</div>
                </li>
            @endforelse

            <!-- Separator -->
            <li class="nav-divider my-3">
                <hr class="text-muted">
            </li>

            <!-- Quick Stats -->
            <li class="nav-header mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Estatísticas</span>
            </li>

            <li class="nav-item mb-2">
                <div class="px-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                @php
                                    try {
                                        $displayProjectCount = $projectCount ?? 0;
                                    } catch (Exception $e) {
                                        $displayProjectCount = 0;
                                    }
                                @endphp
                                <div class="fw-bold text-primary">{{ $displayProjectCount }}</div>
                                <div class="small text-muted">Projetos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                @php
                                    try {
                                        $epicCount = \App\Models\Epic::whereHas('project', function($query) {
                                            $query->where('owner_id', Auth::id())
                                                  ->orWhereHas('users', function($q) {
                                                      $q->where('user_id', Auth::id());
                                                  });
                                        })->count();
                                    } catch (Exception $e) {
                                        $epicCount = 0;
                                    }
                                @endphp
                                <div class="fw-bold text-success">{{ $epicCount }}</div>
                                <div class="small text-muted">Épicos</div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>

            <!-- Separator -->
            <li class="nav-divider my-3">
                <hr class="text-muted">
            </li>

            <!-- Settings & Help -->
            <li class="nav-item mb-1">
                <a class="nav-link" href="#">
                    <i class="bi bi-gear"></i>
                    Configurações
                </a>
            </li>

            <li class="nav-item mb-1">
                <a class="nav-link" href="#">
                    <i class="bi bi-question-circle"></i>
                    Ajuda & Suporte
                </a>
            </li>
        </ul>
    </nav>

    <!-- Footer -->
    <div class="p-3 border-top">
        <div class="d-flex align-items-center justify-content-between">
            <div class="text-muted small">
                <div>SGP Lite v2.0</div>
                <div>Sprint 2 - Épicos</div>
            </div>
            <button class="btn btn-sm btn-outline-secondary" 
                    onclick="toggleTheme()" 
                    title="Alternar tema">
                <i class="bi bi-moon"></i>
            </button>
        </div>
    </div>
</aside>

<style>
.nav-modern .nav-link {
    border-radius: var(--radius-md);
    color: var(--gray-600);
    font-weight: 500;
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    text-decoration: none;
}

.nav-modern .nav-link:hover {
    background-color: var(--gray-100);
    color: var(--primary);
    transform: translateX(4px);
}

.nav-modern .nav-link.active {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    box-shadow: var(--shadow-md);
}

.nav-modern .nav-link i {
    width: 20px;
    margin-right: 0.75rem;
    text-align: center;
}

.nav-divider hr {
    margin: 0;
    opacity: 0.3;
}

.nav-header {
    padding: 0 1rem;
}

.app-sidebar {
    background: var(--white);
    border-right: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
}

@media (max-width: 991.98px) {
    .app-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 1050;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .app-sidebar.show {
        transform: translateX(0);
    }
}

/* Theme toggle functionality */
[data-theme="dark"] .app-sidebar {
    background: var(--dark-surface);
    border-right-color: var(--dark-border);
}

[data-theme="dark"] .nav-modern .nav-link {
    color: var(--gray-300);
}

[data-theme="dark"] .nav-modern .nav-link:hover {
    background-color: var(--dark-border);
    color: var(--primary-light);
}
</style>

<script>
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    // Update icon
    const icon = document.querySelector('.btn-outline-secondary i');
    icon.className = newTheme === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
}

// Load saved theme
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    const icon = document.querySelector('.btn-outline-secondary i');
    if (icon) {
        icon.className = savedTheme === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
    }
});
</script>
