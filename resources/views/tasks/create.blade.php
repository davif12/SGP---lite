<x-app-layout>
    <x-slot name="header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('projects.index') }}" class="text-decoration-none">Projetos</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">{{ $project->name }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('epics.index', $project) }}" class="text-decoration-none">Épicos</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('projects.epics.show', [$project, $epic]) }}" class="text-decoration-none">{{ $epic->name }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('projects.epics.tasks.index', [$project, $epic]) }}" class="text-decoration-none">Tasks</a>
                    </li>
                    <li class="breadcrumb-item active">Nova Task</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 mt-2 text-gradient">Nova Task</h1>
            <p class="text-muted mb-0">Criar nova tarefa para {{ $epic->name }}</p>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-modern">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Criar Nova Task
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.epics.tasks.store', [$project, $epic]) }}">
                        @csrf

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="bi bi-card-text me-1"></i>Título da Task *
                            </label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required
                                   placeholder="Ex: Implementar sistema de login">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-text-paragraph me-1"></i>Descrição
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Descreva os detalhes da task...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Priority -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Prioridade *
                                    </label>
                                    <select class="form-select @error('priority') is-invalid @enderror" 
                                            id="priority" 
                                            name="priority" 
                                            required>
                                        <option value="">Selecione a prioridade</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                            Baixa
                                        </option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                                            Média
                                        </option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                            Alta
                                        </option>
                                        <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>
                                            Crítica
                                        </option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Story Points -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="story_points" class="form-label">
                                        <i class="bi bi-speedometer2 me-1"></i>Story Points
                                    </label>
                                    <select class="form-select @error('story_points') is-invalid @enderror" 
                                            id="story_points" 
                                            name="story_points">
                                        <option value="">Não definido</option>
                                        <option value="1" {{ old('story_points') == '1' ? 'selected' : '' }}>1</option>
                                        <option value="2" {{ old('story_points') == '2' ? 'selected' : '' }}>2</option>
                                        <option value="3" {{ old('story_points') == '3' ? 'selected' : '' }}>3</option>
                                        <option value="5" {{ old('story_points') == '5' ? 'selected' : '' }}>5</option>
                                        <option value="8" {{ old('story_points') == '8' ? 'selected' : '' }}>8</option>
                                        <option value="13" {{ old('story_points') == '13' ? 'selected' : '' }}>13</option>
                                        <option value="21" {{ old('story_points') == '21' ? 'selected' : '' }}>21</option>
                                    </select>
                                    @error('story_points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Estimativa de complexidade (Fibonacci)</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Assigned To -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="assigned_to" class="form-label">
                                        <i class="bi bi-person me-1"></i>Responsável
                                    </label>
                                    <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                            id="assigned_to" 
                                            name="assigned_to">
                                        <option value="">Não atribuído</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                                @if($user->id === $project->owner_id)
                                                    (Dono)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">
                                        <i class="bi bi-calendar3 me-1"></i>Data de Vencimento
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" 
                                           name="due_date" 
                                           value="{{ old('due_date') }}"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Epic Info -->
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>
                                    <strong>Épico:</strong> {{ $epic->name }}<br>
                                    <strong>Projeto:</strong> {{ $project->name }}
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-modern btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Criar Task
                            </button>
                            <a href="{{ route('projects.epics.tasks.index', [$project, $epic]) }}" 
                               class="btn btn-modern btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
