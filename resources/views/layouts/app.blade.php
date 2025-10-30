<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SGP Lite') }}</title>

        <!-- Preconnect for performance -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <script src="{{ mix('js/app.js') }}" defer></script>
        
        <!-- Notifications JavaScript -->
        <script>
            // Load notifications count on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateNotificationBadge();
                
                // Update badge every 30 seconds
                setInterval(updateNotificationBadge, 30000);
                
                // Test Bootstrap dropdowns
                console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');
                
                // Initialize dropdowns manually if needed
                if (typeof bootstrap !== 'undefined') {
                    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                        return new bootstrap.Dropdown(dropdownToggleEl);
                    });
                }
            });

            function updateNotificationBadge() {
                fetch('/api/notifications/unread-count')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.getElementById('notification-badge');
                        if (data.count > 0) {
                            badge.textContent = data.count > 99 ? '99+' : data.count;
                            badge.style.display = 'block';
                        } else {
                            badge.style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error updating notification badge:', error));
            }

            function loadNotifications() {
                const list = document.getElementById('notifications-list');
                list.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Carregando...</span></div></div>';

                fetch('/api/notifications/recent')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.notifications.length > 0) {
                            list.innerHTML = data.notifications.map(notification => `
                                <div class="dropdown-item notification-item ${!notification.read_at ? 'unread' : ''}" 
                                     onclick="handleNotificationClick('${notification.id}', '${notification.url}')">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-2">
                                            <i class="${notification.icon} text-${notification.color}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small">${notification.title}</div>
                                            <div class="text-muted small">${notification.message}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">${notification.time_ago}</div>
                                        </div>
                                        ${!notification.read_at ? '<div class="flex-shrink-0"><span class="badge bg-primary rounded-pill" style="width: 8px; height: 8px;"></span></div>' : ''}
                                    </div>
                                </div>
                            `).join('');
                        } else {
                            list.innerHTML = '<div class="dropdown-item-text text-center text-muted py-3">Nenhuma notificação</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        list.innerHTML = '<div class="dropdown-item-text text-center text-danger py-3">Erro ao carregar notificações</div>';
                    });
            }

            function handleNotificationClick(notificationId, url) {
                // Mark as read
                fetch(`/api/notifications/${notificationId}/read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(() => {
                    updateNotificationBadge();
                    if (url && url !== '#') {
                        window.location.href = url;
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
            }

            function markAllAsRead() {
                fetch('/api/notifications/read-all', {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadge();
                        loadNotifications();
                    }
                })
                .catch(error => console.error('Error marking all as read:', error));
            }

            // Global Search Functionality
            let searchTimeout;
            const searchInput = document.getElementById('global-search');
            const searchResults = document.getElementById('search-results');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();

                    if (query.length < 2) {
                        hideSearchResults();
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        performGlobalSearch(query);
                    }, 300);
                });

                searchInput.addEventListener('focus', function() {
                    if (this.value.trim().length >= 2) {
                        searchResults.style.display = 'block';
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        hideSearchResults();
                    }
                });
            }

            function performGlobalSearch(query) {
                searchResults.innerHTML = '<div class="p-3 text-center"><div class="spinner-border spinner-border-sm" role="status"></div></div>';
                searchResults.style.display = 'block';

                fetch('/api/search/global?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.results.length > 0) {
                            displaySearchResults(data.results);
                        } else {
                            searchResults.innerHTML = '<div class="p-3 text-muted text-center">Nenhum resultado encontrado</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.innerHTML = '<div class="p-3 text-danger text-center">Erro na busca</div>';
                    });
            }

            function displaySearchResults(results) {
                const html = results.map(result => 
                    '<a href="' + result.url + '" class="d-block p-3 text-decoration-none border-bottom search-result-item">' +
                        '<div class="d-flex align-items-start">' +
                            '<div class="flex-shrink-0 me-2">' +
                                '<i class="' + result.icon + ' text-' + result.color + '"></i>' +
                            '</div>' +
                            '<div class="flex-grow-1">' +
                                '<div class="fw-semibold text-dark">' + result.title + '</div>' +
                                '<div class="text-muted small">' + (result.description || '') + '</div>' +
                                '<div class="d-flex gap-2 mt-1">' +
                                    '<span class="badge bg-' + result.color + ' text-capitalize">' + result.type + '</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</a>'
                ).join('');

                searchResults.innerHTML = html + 
                    '<div class="p-2 border-top bg-light text-center">' +
                    '<a href="/search?search=' + encodeURIComponent(searchInput.value) + '" class="btn btn-sm btn-outline-primary">Ver todos os resultados</a>' +
                    '</div>';
            }

            function hideSearchResults() {
                searchResults.style.display = 'none';
            }

            // Real-time notifications setup
            if (typeof window.Echo !== 'undefined') {
                // Listen for notifications on user's private channel
                window.Echo.private(`user.{{ auth()->id() }}`)
                    .listen('.notification.sent', (e) => {
                        handleRealTimeNotification(e.notification);
                        updateNotificationBadge();
                    });

                // Listen for online users
                window.Echo.join('online-users')
                    .here((users) => {
                        console.log('Users currently online:', users);
                        updateOnlineUsers(users);
                    })
                    .joining((user) => {
                        console.log('User joined:', user.name);
                        addOnlineUser(user);
                        showToast(`${user.name} entrou online`, 'info');
                    })
                    .leaving((user) => {
                        console.log('User left:', user.name);
                        removeOnlineUser(user);
                        showToast(`${user.name} saiu offline`, 'secondary');
                    });

                // Listen for task updates on current project (if on project page)
                @if(request()->route() && request()->route()->hasParameter('project'))
                window.Echo.private('project.{{ request()->route()->parameter("project")->id ?? "" }}')
                    .listen('.task.updated', (e) => {
                        handleTaskUpdate(e);
                    });
                @endif
            }

            function handleRealTimeNotification(notification) {
                // Show toast notification
                showToast(notification.message, 'primary', notification.icon);
                
                // Play notification sound (optional)
                playNotificationSound();
                
                // Update notification dropdown if it's open
                if (document.querySelector('.notification-dropdown.show')) {
                    loadNotifications();
                }
            }

            function handleTaskUpdate(data) {
                // Update task in Kanban board if present
                const taskCard = document.querySelector(`[data-task-id="${data.task.id}"]`);
                if (taskCard) {
                    updateTaskCard(taskCard, data.task);
                    showToast(`Task "${data.task.title}" foi atualizada por ${data.user.name}`, 'info');
                }
            }

            function updateTaskCard(card, task) {
                // Update task status badge
                const statusBadge = card.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.textContent = task.status_label;
                    statusBadge.className = `badge status-badge bg-${getStatusColor(task.status)}`;
                }
                
                // Update assigned user
                const assignedUser = card.querySelector('.assigned-user');
                if (assignedUser && task.assigned_user) {
                    assignedUser.textContent = task.assigned_user.name;
                }
            }

            function getStatusColor(status) {
                const colors = {
                    'todo': 'secondary',
                    'in_progress': 'primary', 
                    'review': 'warning',
                    'done': 'success'
                };
                return colors[status] || 'secondary';
            }

            function updateOnlineUsers(users) {
                // Update online users indicator
                const onlineCount = document.getElementById('online-users-count');
                if (onlineCount) {
                    onlineCount.textContent = users.length;
                }
            }

            function addOnlineUser(user) {
                // Add user to online list
                updateOnlineUsers([user]); // Simplified for demo
            }

            function removeOnlineUser(user) {
                // Remove user from online list
                updateOnlineUsers([]); // Simplified for demo
            }

            function showToast(message, type = 'info', icon = null) {
                // Create toast notification
                const toast = document.createElement('div');
                toast.className = `toast align-items-center text-white bg-${type} border-0`;
                toast.setAttribute('role', 'alert');
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            ${icon ? `<i class="${icon} me-2"></i>` : ''}
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                
                // Add to toast container
                let toastContainer = document.getElementById('toast-container');
                if (!toastContainer) {
                    toastContainer = document.createElement('div');
                    toastContainer.id = 'toast-container';
                    toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                    toastContainer.style.zIndex = '9999';
                    document.body.appendChild(toastContainer);
                }
                
                toastContainer.appendChild(toast);
                
                // Show toast
                const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
                bsToast.show();
                
                // Remove from DOM after hiding
                toast.addEventListener('hidden.bs.toast', () => {
                    toast.remove();
                });
            }

            function playNotificationSound() {
                // Play a subtle notification sound
                try {
                    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
                    audio.volume = 0.1;
                    audio.play().catch(() => {}); // Ignore errors if audio fails
                } catch (e) {
                    // Ignore audio errors
                }
            }
        </script>
        
        <style>
            .notification-dropdown .notification-item {
                cursor: pointer;
                transition: background-color 0.2s;
            }
            
            .notification-dropdown .notification-item:hover {
                background-color: var(--bs-gray-100);
            }
            
            .notification-dropdown .notification-item.unread {
                background-color: var(--bs-blue-50);
            }
            
            .notification-dropdown .notification-item.unread:hover {
                background-color: var(--bs-blue-100);
            }
            
            /* Search Styles */
            .search-result-item:hover {
                background-color: var(--bs-gray-50);
            }
            
            #search-results {
                border-top: none !important;
            }
            
            #global-search:focus + #search-results {
                display: block;
            }
        </style>
    </head>
    <body class="app-layout">
        <!-- Header -->
        @include('layouts.partials.navbar')

        <!-- Main Layout -->
        <div class="d-flex flex-grow-1">
            <!-- Sidebar -->
            @include('layouts.partials.sidebar')

            <!-- Main Content -->
            <main class="app-main flex-grow-1">
                <!-- Page Header -->
                @if(isset($header))
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        {{ $header }}
                    </div>
                </div>
                @endif

                <!-- Page Content -->
                <div class="content-wrapper">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Footer -->
        @include('layouts.partials.footer')

        <!-- Toast Container -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay d-lg-none" onclick="toggleSidebar()"></div>

        <script>
            // Sidebar toggle for mobile
            function toggleSidebar() {
                const sidebar = document.querySelector('.app-sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                const sidebar = document.querySelector('.app-sidebar');
                const sidebarToggle = document.querySelector('.sidebar-toggle');
                
                if (window.innerWidth < 992 && 
                    !sidebar.contains(e.target) && 
                    !sidebarToggle.contains(e.target) &&
                    sidebar.classList.contains('show')) {
                    toggleSidebar();
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                const sidebar = document.querySelector('.app-sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });
        </script>

        <style>
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }

            @media (max-width: 991.98px) {
                .app-sidebar {
                    position: fixed;
                    top: 0;
                    left: 0;
                    height: 100vh;
                    width: 280px;
                    z-index: 1050;
                    transform: translateX(-100%);
                    transition: transform 0.3s ease;
                }

                .app-sidebar.show {
                    transform: translateX(0);
                }
            }
        </style>
    </body>
</html>
