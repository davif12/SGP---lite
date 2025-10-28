<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <x-application-logo class="d-inline-block align-text-top" style="height: 40px;" />
        </a>

        <!-- Toggle button for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                       href="{{ route('dashboard') }}">
                        {{ __('Dashboard') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" 
                       href="{{ route('projects.index') }}">
                        {{ __('Projetos') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('board.*') ? 'active' : '' }}" 
                       href="{{ route('board.index') }}">
                        {{ __('Board') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('search.*') ? 'active' : '' }}" 
                       href="{{ route('search.index') }}">
                        {{ __('Busca') }}
                    </a>
                </li>
            </ul>

            <!-- Global Search -->
            <div class="d-flex flex-grow-1 justify-content-center mx-4">
                <div class="position-relative" style="max-width: 500px; width: 100%;">
                    <input type="text" 
                           class="form-control" 
                           id="global-search" 
                           placeholder="Buscar projetos, épicos, tasks..." 
                           autocomplete="off">
                    <div class="position-absolute top-100 start-0 w-100 bg-white border rounded-bottom shadow-lg" 
                         id="search-results" 
                         style="display: none; z-index: 1050; max-height: 400px; overflow-y: auto;">
                    </div>
                </div>
            </div>

            <!-- Notifications & User Dropdown -->
            <ul class="navbar-nav">
                <!-- Notifications Dropdown -->
                <li class="nav-item dropdown me-2">
                    <button class="btn btn-link position-relative p-2" type="button" data-bs-toggle="dropdown" onclick="loadNotifications()">
                        <i class="bi bi-bell fs-5 text-dark"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-badge" style="display: none;">
                            0
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Notificações</h6>
                            <div>
                                <button class="btn btn-link btn-sm p-0 me-2" onclick="markAllAsRead()" title="Marcar todas como lidas">
                                    <i class="bi bi-check2-all"></i>
                                </button>
                                <a href="{{ route('notifications.index') }}" class="btn btn-link btn-sm p-0" title="Ver todas">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div id="notifications-list">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                
                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">{{ Auth::user()->name }}</h6>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i>{{ __('Perfil') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('notifications.index') }}">
                                <i class="bi bi-bell me-2"></i>{{ __('Notificações') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('search.index') }}">
                                <i class="bi bi-search me-2"></i>{{ __('Busca Avançada') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>{{ __('Sair') }}
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- Notifications Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="dropdown">
                    <button class="btn btn-link position-relative p-2" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" onclick="loadNotifications()">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-badge" style="display: none;">
                            0
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Notificações</h6>
                            <div>
                                <button class="btn btn-link btn-sm p-0 me-2" onclick="markAllAsRead()" title="Marcar todas como lidas">
                                    <i class="bi bi-check2-all"></i>
                                </button>
                                <a href="{{ route('notifications.index') }}" class="btn btn-link btn-sm p-0" title="Ver todas">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div id="notifications-list">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
