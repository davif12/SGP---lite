<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gradient">Busca Avançada</h1>
                <p class="text-muted mb-0">Encontre tasks com filtros detalhados</p>
            </div>
            <div>
                <button class="btn btn-modern btn-secondary btn-sm" onclick="clearFilters()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Limpar Filtros
                </button>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card-modern">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtros</h6>
                </div>
                <div class="card-body">
                    <form id="search-form">
                        <!-- Text Search -->
                        <div class="mb-3">
                            <label class="form-label">Buscar por texto</label>
                            <input type="text" class="form-control" name="search" id="search-input" 
                                   placeholder="Título ou descrição..." value="{{ request('search') }}">
                        </div>

                        <!-- Project Filter -->
                        <div class="mb-3">
                            <label class="form-label">Projeto</label>
                            <select class="form-select" name="project_id" id="project-filter">
                                <option value="">Todos os projetos</option>
                            </select>
                        </div>

                        <!-- Epic Filter -->
                        <div class="mb-3">
                            <label class="form-label">Épico</label>
                            <select class="form-select" name="epic_id" id="epic-filter">
                                <option value="">Todos os épicos</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="todo" id="status-todo">
                                    <label class="form-check-label" for="status-todo">A Fazer</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="in_progress" id="status-progress">
                                    <label class="form-check-label" for="status-progress">Em Progresso</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="review" id="status-review">
                                    <label class="form-check-label" for="status-review">Em Revisão</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="done" id="status-done">
                                    <label class="form-check-label" for="status-done">Concluído</label>
                                </div>
                            </div>
                        </div>

                        <!-- Priority Filter -->
                        <div class="mb-3">
                            <label class="form-label">Prioridade</label>
                            <div class="form-check-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="priority[]" value="low" id="priority-low">
                                    <label class="form-check-label" for="priority-low">Baixa</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="priority[]" value="medium" id="priority-medium">
                                    <label class="form-check-label" for="priority-medium">Média</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="priority[]" value="high" id="priority-high">
                                    <label class="form-check-label" for="priority-high">Alta</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="priority[]" value="critical" id="priority-critical">
                                    <label class="form-check-label" for="priority-critical">Crítica</label>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned To Filter -->
                        <div class="mb-3">
                            <label class="form-label">Atribuído para</label>
                            <select class="form-select" name="assigned_to" id="assigned-filter">
                                <option value="">Qualquer pessoa</option>
                                <option value="me">Minhas tasks</option>
                                <option value="unassigned">Não atribuídas</option>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="mb-3">
                            <label class="form-label">Data de criação</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" class="form-control form-control-sm" name="date_from" placeholder="De">
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control form-control-sm" name="date_to" placeholder="Até">
                                </div>
                            </div>
                        </div>

                        <!-- Due Date Range -->
                        <div class="mb-3">
                            <label class="form-label">Data de vencimento</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" class="form-control form-control-sm" name="due_date_from" placeholder="De">
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control form-control-sm" name="due_date_to" placeholder="Até">
                                </div>
                            </div>
                        </div>

                        <!-- Story Points Range -->
                        <div class="mb-3">
                            <label class="form-label">Story Points</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" name="story_points_min" placeholder="Min" min="1">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" name="story_points_max" placeholder="Max" min="1">
                                </div>
                            </div>
                        </div>

                        <!-- Sort Options -->
                        <div class="mb-3">
                            <label class="form-label">Ordenar por</label>
                            <select class="form-select form-select-sm" name="sort_by">
                                <option value="created_at">Data de criação</option>
                                <option value="updated_at">Última atualização</option>
                                <option value="title">Título</option>
                                <option value="priority">Prioridade</option>
                                <option value="status">Status</option>
                                <option value="due_date">Data de vencimento</option>
                                <option value="story_points">Story Points</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <select class="form-select form-select-sm" name="sort_order">
                                <option value="desc">Decrescente</option>
                                <option value="asc">Crescente</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-modern btn-primary w-100">
                            <i class="bi bi-search me-1"></i>Buscar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="col-lg-9">
            <div class="card-modern">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Resultados</h6>
                        <small class="text-muted" id="results-count">Carregando...</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-modern btn-outline-secondary btn-sm" onclick="toggleView('list')" id="list-view-btn">
                            <i class="bi bi-list"></i>
                        </button>
                        <button class="btn btn-modern btn-outline-secondary btn-sm" onclick="toggleView('grid')" id="grid-view-btn">
                            <i class="bi bi-grid"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="search-results">
                        <div class="text-center py-5">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination-container" class="d-flex justify-content-center mt-4" style="display: none;">
            </div>
        </div>
    </div>
</x-app-layout>

<script>
let currentView = 'list';
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadFilterOptions();
    performSearch();
    
    // Form submission
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        performSearch();
    });
    
    // Real-time search on text input
    let searchTimeout;
    document.getElementById('search-input').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            performSearch();
        }, 500);
    });
    
    // Filter changes
    document.querySelectorAll('select, input[type="checkbox"], input[type="date"], input[type="number"]').forEach(element => {
        element.addEventListener('change', function() {
            currentPage = 1;
            performSearch();
        });
    });
});

function loadFilterOptions() {
    fetch('/api/search/filter-options')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate projects
                const projectSelect = document.getElementById('project-filter');
                data.options.projects.forEach(project => {
                    const option = document.createElement('option');
                    option.value = project.id;
                    option.textContent = project.name;
                    projectSelect.appendChild(option);
                });
                
                // Populate epics
                const epicSelect = document.getElementById('epic-filter');
                data.options.epics.forEach(epic => {
                    const option = document.createElement('option');
                    option.value = epic.id;
                    option.textContent = epic.project.name + ' - ' + epic.name;
                    epicSelect.appendChild(option);
                });
                
                // Populate users
                const assignedSelect = document.getElementById('assigned-filter');
                data.options.users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    assignedSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading filter options:', error));
}

function performSearch() {
    const formData = new FormData(document.getElementById('search-form'));
    formData.append('page', currentPage);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    
    document.getElementById('search-results').innerHTML = 
        '<div class="text-center py-5"><div class="spinner-border" role="status"></div></div>';
    
    fetch('/api/search/tasks?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResults(data.tasks, data.pagination);
            } else {
                document.getElementById('search-results').innerHTML = 
                    '<div class="text-center py-5 text-danger">Erro na busca</div>';
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            document.getElementById('search-results').innerHTML = 
                '<div class="text-center py-5 text-danger">Erro na busca</div>';
        });
}

function displayResults(tasks, pagination) {
    const resultsContainer = document.getElementById('search-results');
    const countElement = document.getElementById('results-count');
    
    countElement.textContent = `${pagination.total} resultado(s) encontrado(s)`;
    
    if (tasks.length === 0) {
        resultsContainer.innerHTML = 
            '<div class="text-center py-5 text-muted">' +
                '<i class="bi bi-search display-4 mb-3"></i>' +
                '<h5>Nenhuma task encontrada</h5>' +
                '<p>Tente ajustar os filtros de busca</p>' +
            '</div>';
        return;
    }
    
    if (currentView === 'list') {
        displayListView(tasks);
    } else {
        displayGridView(tasks);
    }
    
    displayPagination(pagination);
}

function displayListView(tasks) {
    const html = tasks.map(task => `
        <div class="border-bottom p-3 task-item">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-2">
                        <h6 class="mb-0 me-2">
                            <a href="${getTaskUrl(task)}" class="text-decoration-none">${task.title}</a>
                        </h6>
                        <span class="badge bg-${task.status === 'todo' ? 'secondary' : task.status === 'in_progress' ? 'primary' : task.status === 'review' ? 'warning' : 'success'} me-2">
                            ${getStatusLabel(task.status)}
                        </span>
                        <span class="badge bg-${task.priority === 'low' ? 'success' : task.priority === 'medium' ? 'warning' : task.priority === 'high' ? 'danger' : 'dark'}">
                            ${getPriorityLabel(task.priority)}
                        </span>
                    </div>
                    <p class="text-muted mb-2 small">${task.description || 'Sem descrição'}</p>
                    <div class="d-flex align-items-center gap-3 small text-muted">
                        <span><i class="bi bi-folder me-1"></i>${task.epic.project.name}</span>
                        <span><i class="bi bi-collection me-1"></i>${task.epic.name}</span>
                        ${task.assigned_user ? `<span><i class="bi bi-person me-1"></i>${task.assigned_user.name}</span>` : ''}
                        ${task.story_points ? `<span><i class="bi bi-star me-1"></i>${task.story_points} pts</span>` : ''}
                        ${task.due_date ? `<span><i class="bi bi-calendar me-1"></i>${formatDate(task.due_date)}</span>` : ''}
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-muted">${formatDate(task.created_at)}</small>
                </div>
            </div>
        </div>
    `).join('');
    
    document.getElementById('search-results').innerHTML = html;
}

function displayGridView(tasks) {
    const html = '<div class="row p-3">' + tasks.map(task => `
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100 task-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0">
                            <a href="${getTaskUrl(task)}" class="text-decoration-none">${task.title}</a>
                        </h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="${getTaskUrl(task)}">Ver detalhes</a></li>
                            </ul>
                        </div>
                    </div>
                    <p class="card-text text-muted small">${task.description || 'Sem descrição'}</p>
                    <div class="mb-2">
                        <span class="badge bg-${task.status === 'todo' ? 'secondary' : task.status === 'in_progress' ? 'primary' : task.status === 'review' ? 'warning' : 'success'} me-1">
                            ${getStatusLabel(task.status)}
                        </span>
                        <span class="badge bg-${task.priority === 'low' ? 'success' : task.priority === 'medium' ? 'warning' : task.priority === 'high' ? 'danger' : 'dark'}">
                            ${getPriorityLabel(task.priority)}
                        </span>
                    </div>
                    <div class="small text-muted">
                        <div><i class="bi bi-folder me-1"></i>${task.epic.project.name}</div>
                        <div><i class="bi bi-collection me-1"></i>${task.epic.name}</div>
                        ${task.assigned_user ? `<div><i class="bi bi-person me-1"></i>${task.assigned_user.name}</div>` : ''}
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <small class="text-muted">${formatDate(task.created_at)}</small>
                </div>
            </div>
        </div>
    `).join('') + '</div>';
    
    document.getElementById('search-results').innerHTML = html;
}

function displayPagination(pagination) {
    const container = document.getElementById('pagination-container');
    
    if (pagination.last_page <= 1) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'flex';
    
    let html = '<nav><ul class="pagination">';
    
    // Previous
    if (pagination.current_page > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${pagination.current_page - 1})">Anterior</a></li>`;
    }
    
    // Pages
    for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
        html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="goToPage(${i})">${i}</a>
                 </li>`;
    }
    
    // Next
    if (pagination.current_page < pagination.last_page) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${pagination.current_page + 1})">Próximo</a></li>`;
    }
    
    html += '</ul></nav>';
    container.innerHTML = html;
}

function goToPage(page) {
    currentPage = page;
    performSearch();
}

function toggleView(view) {
    currentView = view;
    document.getElementById('list-view-btn').classList.toggle('active', view === 'list');
    document.getElementById('grid-view-btn').classList.toggle('active', view === 'grid');
    performSearch();
}

function clearFilters() {
    document.getElementById('search-form').reset();
    currentPage = 1;
    performSearch();
}

function getTaskUrl(task) {
    return `/projects/${task.epic.project.id}/epics/${task.epic.id}/tasks/${task.id}`;
}

function getStatusLabel(status) {
    const labels = {
        'todo': 'A Fazer',
        'in_progress': 'Em Progresso', 
        'review': 'Em Revisão',
        'done': 'Concluído'
    };
    return labels[status] || status;
}

function getPriorityLabel(priority) {
    const labels = {
        'low': 'Baixa',
        'medium': 'Média',
        'high': 'Alta', 
        'critical': 'Crítica'
    };
    return labels[priority] || priority;
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('pt-BR');
}
</script>

<style>
.form-check-group .form-check {
    margin-bottom: 0.5rem;
}

.task-item:hover {
    background-color: var(--bs-gray-50);
}

.task-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.btn.active {
    background-color: var(--bs-primary);
    color: white;
}
</style>
