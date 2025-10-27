<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'SGP Lite') }}</title>
    
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

                    <!-- Login Card -->
                    <div class="card-modern">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h2 class="h4 mb-1">Bem-vindo de volta!</h2>
                                <p class="text-muted small">Faça login para acessar sua conta</p>
                            </div>

                            <!-- Session Status -->
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

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

                            <form method="POST" action="{{ route('login') }}" class="form-modern">
                                @csrf

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
                                           autofocus
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
                                               autocomplete="current-password"
                                               placeholder="Sua senha">
                                        <button type="button" 
                                                class="btn btn-link position-absolute top-50 end-0 translate-middle-y pe-3" 
                                                onclick="togglePassword()"
                                                style="border: none; background: none;">
                                            <i class="bi bi-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Remember Me -->
                                <div class="mb-3 form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="remember_me" 
                                           name="remember">
                                    <label class="form-check-label" for="remember_me">
                                        Lembrar de mim
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-modern btn-primary">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Entrar
                                    </button>
                                </div>

                                <!-- Forgot Password -->
                                @if (Route::has('password.request'))
                                    <div class="text-center">
                                        <a href="{{ route('password.request') }}" class="text-decoration-none">
                                            <i class="bi bi-question-circle me-1"></i>Esqueceu sua senha?
                                        </a>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center mt-4">
                        <p class="text-muted">
                            Não tem uma conta? 
                            <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">
                                Criar conta
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
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
