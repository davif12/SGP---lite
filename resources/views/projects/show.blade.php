<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.index') }}">Projetos</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $project->name }}</li>
                    </ol>
                </nav>
                <h2 class="h3 mb-0 mt-2">{{ $project->name }}</h2>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('epics.index', $project) }}" class="btn btn-primary">
                    <i class="bi bi-journal-text me-1"></i>Épicos
                </a>
                @if($project->isOwner(auth()->user()))
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Informações do Projeto -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Projeto</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nome</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $project->name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Dono</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $project->owner->name }}</p>
                        </div>
                        
                        @if($project->description)
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-500">Descrição</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $project->description }}</p>
                            </div>
                        @endif
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Criado em</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $project->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Última atualização</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $project->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Membros do Projeto -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Membros do Projeto</h3>
                        @if($project->isOwner(auth()->user()))
                            <button onclick="document.getElementById('add-member-form').classList.toggle('hidden')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Adicionar Membro
                            </button>
                        @endif
                    </div>

                    @if($project->isOwner(auth()->user()))
                        <div id="add-member-form" class="hidden mb-6 p-4 bg-gray-50 rounded-lg">
                            <form method="POST" action="{{ route('projects.members.add', $project) }}">
                                @csrf
                                <div class="flex space-x-4">
                                    <div class="flex-1">
                                        <x-label for="email" :value="__('Email do usuário')" />
                                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" required />
                                        @error('email')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex items-end">
                                        <x-button>
                                            {{ __('Adicionar') }}
                                        </x-button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="space-y-3">
                        @foreach($project->users as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->pivot->role === 'owner' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $user->pivot->role === 'owner' ? 'Dono' : 'Membro' }}
                                    </span>
                                    @if($project->isOwner(auth()->user()) && $user->pivot->role !== 'owner')
                                        <form method="POST" action="{{ route('projects.members.remove', [$project, $user]) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Tem certeza que deseja remover este membro?')">
                                                Remover
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($project->isOwner(auth()->user()))
                <!-- Zona de Perigo -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-red-600 mb-4">Zona de Perigo</h3>
                        <p class="text-sm text-gray-600 mb-4">Uma vez que você excluir um projeto, não há como voltar atrás. Por favor, tenha certeza.</p>
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Tem certeza que deseja excluir este projeto? Esta ação não pode ser desfeita.')">
                                Excluir Projeto
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
