<x-app-layout>
    <x-slot name="header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('projects.index') }}">Projetos</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('epics.index', $project) }}">Épicos</a>
                    </li>
                    <li class="breadcrumb-item active">Novo Épico</li>
                </ol>
            </nav>
            <h2 class="h3 mb-0 mt-2">Criar Novo Épico</h2>
        </div>
    </x-slot>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-journal-plus me-2"></i>Novo Épico para {{ $project->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('projects.epics.store', $project) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome do Épico <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       placeholder="Ex: Sistema de Autenticação">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          placeholder="Descreva o objetivo e escopo deste épico...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status" 
                                                required>
                                            <option value="backlog" {{ old('status', 'backlog') == 'backlog' ? 'selected' : '' }}>
                                                Backlog
                                            </option>
                                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>
                                                Em Progresso
                                            </option>
                                            <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>
                                                Concluído
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Prioridade <span class="text-danger">*</span></label>
                                        <select class="form-select @error('priority') is-invalid @enderror" 
                                                id="priority" 
                                                name="priority" 
                                                required>
                                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                                Baixa
                                            </option>
                                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>
                                                Média
                                            </option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                                Alta
                                            </option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('epics.index', $project) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>Criar Épico
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
