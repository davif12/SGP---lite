<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-dark px-3">
        <div class="container-fluid">
            <!-- Mobile Sidebar Toggle -->
            <button class="btn btn-link text-white d-lg-none sidebar-toggle me-3" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                <i class="bi bi-list fs-4"></i>
            </button>

            <!-- Brand -->
            <a class="navbar-brand fw-bold text-gradient d-flex align-items-center" href="{{ route('dashboard') }}">
                <i class="bi bi-kanban me-2 fs-4"></i>
                <span class="text-white">SGP Lite</span>
            </a>

            <!-- Search Bar (Desktop) -->
            <div class="d-none d-md-flex flex-grow-1 mx-4">
                <div class="position-relative w-100" style="max-width: 400px;">
                    <input type="search" 
                           class="form-control bg-white bg-opacity-10 border-0 text-white placeholder-white-50" 
                           placeholder="Buscar projetos, épicos..."
                           style="padding-left: 2.5rem;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-white-50"></i>
                </div>
            </div>

            <!-- Right Side Items -->
            <div class="d-flex align-items-center gap-2">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-link text-white position-relative" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            aria-label="Notifications">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="width: 320px;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Notificações</span>
                            <small class="text-primary">Marcar todas como lidas</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-3" href="#">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-plus text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fw-semibold">Novo membro adicionado</div>
                                        <div class="text-muted small">João foi adicionado ao projeto Alpha</div>
                                        <div class="text-muted small">2 min atrás</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-3" href="#">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-circle text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fw-semibold">Épico concluído</div>
                                        <div class="text-muted small">Sistema de Autenticação foi finalizado</div>
                                        <div class="text-muted small">1 hora atrás</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="text-center">
                            <a class="dropdown-item text-primary" href="#">Ver todas as notificações</a>
                        </li>
                    </ul>
                </div>

                <!-- Quick Actions -->
                <div class="dropdown">
                    <button class="btn btn-link text-white" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            aria-label="Quick actions">
                        <i class="bi bi-plus-circle fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                        <li class="dropdown-header">Criar novo</li>
                        <li>
                            <a class="dropdown-item" href="{{ route('projects.create') }}">
                                <i class="bi bi-folder-plus me-2"></i>Projeto
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="showCreateEpicModal()">
                                <i class="bi bi-journal-plus me-2"></i>Épico
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="showCreateTaskModal()">
                                <i class="bi bi-plus-square me-2"></i>Tarefa
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-link text-white d-flex align-items-center" 
                            type="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-2" 
                             style="width: 32px; height: 32px;">
                            <i class="bi bi-person text-white"></i>
                        </div>
                        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                        <li class="dropdown-header">
                            <div class="text-muted small">Logado como</div>
                            <div class="fw-semibold">{{ Auth::user()->name }}</div>
                            <div class="text-muted small">{{ Auth::user()->email }}</div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person me-2"></i>Meu Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i>Configurações
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-question-circle me-2"></i>Ajuda
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>

<style>
.placeholder-white-50::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.5) !important;
}

.navbar-brand:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.dropdown-menu {
    border-radius: 0.75rem;
    padding: 0.5rem 0;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.15s ease;
}

.dropdown-item:hover {
    background-color: var(--gray-100);
    transform: translateX(2px);
}

.btn:focus-visible {
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.5);
}
</style>
