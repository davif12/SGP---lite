<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro - {{ config('app.name', 'SGP Lite') }}</title>
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Scripts -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="bg-gray-50">
    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <!-- Logo and Title -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="bi bi-kanban text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <h1 class="h3 mb-1 text-gradient">SGP Lite</h1>
                        <p class="text-muted">Sistema de Gestão de Projetos</p>
                    </div>

                    <!-- Register Card -->
                    <div class="card-modern">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h2 class="h4 mb-1">Criar Nova Conta</h2>
                                <p class="text-muted small">Preencha os dados para começar</p>
                            </div>

                            <!-- Validation Errors -->
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Ops! Algo deu errado.</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}" class="form-modern">
                                @csrf

                                <!-- Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="bi bi-person me-1"></i>Nome Completo
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           required 
                                           autofocus
                                           placeholder="Seu nome completo">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email Address -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required
                                           placeholder="seu@email.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock me-1"></i>Senha
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required 
                                               autocomplete="new-password"
                                               placeholder="Mínimo 8 caracteres">
                                        <button type="button" 
                                                class="btn btn-link position-absolute top-50 end-0 translate-middle-y pe-3" 
                                                onclick="togglePassword('password', 'toggleIcon1')"
                                                style="border: none; background: none;">
                                            <i class="bi bi-eye" id="toggleIcon1"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="bi bi-shield-check me-1"></i>Confirmar Senha
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" 
                                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               required
                                               placeholder="Digite a senha novamente">
                                        <button type="button" 
                                                class="btn btn-link position-absolute top-50 end-0 translate-middle-y pe-3" 
                                                onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                                                style="border: none; background: none;">
                                            <i class="bi bi-eye" id="toggleIcon2"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" required>
                                    <label class="form-check-label small" for="terms">
                                        Concordo com os <a href="#" class="text-decoration-none">Termos de Uso</a> 
                                        e <a href="#" class="text-decoration-none">Política de Privacidade</a>
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-modern btn-primary">
                                        <i class="bi bi-person-plus me-1"></i>Criar Conta
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Já tem uma conta? 
                            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
                                Fazer login
                            </a>
                        </p>
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            &copy; {{ date('Y') }} SGP Lite. Todos os direitos reservados.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        }

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            
            if (password.length === 0) {
                return;
            }
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // You can add visual feedback here if needed
        });
    </script>
</body>
</html>
