<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Master Help Desk - Connexion</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Left Side - Image Section */
        .left-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem;
            overflow: hidden;
        }
        
        .left-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .floating-shapes {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            animation: float 20s infinite ease-in-out;
        }
        
        .shape1 {
            width: 300px;
            height: 300px;
            top: -50px;
            left: -50px;
            animation-delay: 0s;
        }
        
        .shape2 {
            width: 200px;
            height: 200px;
            bottom: -30px;
            right: -30px;
            animation-delay: 7s;
        }
        
        .shape3 {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 14s;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-30px, 30px) scale(0.9); }
        }
        
        .left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            max-width: 500px;
        }
        
        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 3rem;
            animation: fadeInDown 0.8s ease-out;
        }
        
        .brand i {
            font-size: 48px;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
        }
        
        .brand-text {
            font-size: 32px;
            font-weight: 800;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .illustration {
            width: 100%;
            max-width: 450px;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .illustration-icon {
            font-size: 180px;
            color: rgba(255, 255, 255, 0.9);
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
            animation: pulse 3s infinite ease-in-out;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .left-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
            animation: fadeInUp 1s ease-out 0.5s both;
        }
        
        .left-description {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.7;
            animation: fadeInUp 1s ease-out 0.7s both;
        }
        
        .features-list {
            margin-top: 3rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            animation: fadeInUp 1s ease-out 0.9s both;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .feature-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(10px);
        }
        
        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .feature-text {
            text-align: left;
            flex: 1;
        }
        
        .feature-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        
        .feature-desc {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
        }
        
        /* Right Side - Login Form */
        .right-section {
            flex: 1;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
            position: relative;
        }
        
        .right-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
            animation: fadeInRight 0.8s ease-out;
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .login-header {
            margin-bottom: 3rem;
        }
        
        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #ede9fe;
            color: #6366f1;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .login-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #1e293b 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .login-subtitle {
            font-size: 1rem;
            color: #64748b;
            line-height: 1.6;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #334155;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        
        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            color: #1e293b;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #6366f1;
            background: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        
        .form-input:focus + .input-icon {
            color: #6366f1;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: #6366f1;
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 4px;
            cursor: pointer;
            accent-color: #6366f1;
        }
        
        .checkbox-label {
            font-size: 0.9rem;
            color: #475569;
            cursor: pointer;
        }
        
        .forgot-link {
            font-size: 0.9rem;
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .forgot-link:hover {
            color: #4f46e5;
        }
        
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .divider-line {
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        
        .divider-text {
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .social-login {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .social-btn {
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .social-btn:hover {
            border-color: #6366f1;
            color: #6366f1;
            background: #f8fafc;
        }
        
        .social-btn i {
            font-size: 18px;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.95rem;
            color: #64748b;
        }
        
        .signup-link a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .signup-link a:hover {
            color: #4f46e5;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
            }
            
            .left-section {
                min-height: 50vh;
                padding: 3rem 2rem;
            }
            
            .left-title {
                font-size: 2rem;
            }
            
            .left-description {
                font-size: 1rem;
            }
            
            .features-list {
                margin-top: 2rem;
            }
            
            .illustration-icon {
                font-size: 120px;
            }
            
            .right-section {
                padding: 3rem 2rem;
            }
            
            .login-title {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 640px) {
            .left-section {
                padding: 2rem 1.5rem;
            }
            
            .brand-text {
                font-size: 24px;
            }
            
            .brand i {
                font-size: 36px;
            }
            
            .left-title {
                font-size: 1.75rem;
            }
            
            .illustration-icon {
                font-size: 100px;
            }
            
            .features-list {
                gap: 1rem;
            }
            
            .feature-item {
                padding: 0.75rem 1rem;
            }
            
            .right-section {
                padding: 2rem 1.5rem;
            }
            
            .login-title {
                font-size: 1.75rem;
            }
            
            .social-login {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Styles -->
    @livewireStyles
</head>
<body>
    <div class="container">
        <!-- Left Section - Image/Illustration -->
        <div class="left-section">
            <div class="floating-shapes">
                <div class="shape shape1"></div>
                <div class="shape shape2"></div>
                <div class="shape shape3"></div>
            </div>
            
            <div class="left-content">
                <div class="brand">
                    <i class="fas fa-headset"></i>
                    <span class="brand-text">Master Help Desk</span>
                </div>
                
                <div class="illustration">
                    <i class="fas fa-laptop-code illustration-icon"></i>
                </div>
                
                <h1 class="left-title">
                    Bienvenue sur votre plateforme
                </h1>
                
                <p class="left-description">
                    Gérez vos tickets, projets et support client avec efficacité et simplicité
                </p>
                
                <div class="features-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-text">
                            <div class="feature-title">Sécurité maximale</div>
                            <div class="feature-desc">Vos données sont protégées</div>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="feature-text">
                            <div class="feature-title">Rapide et efficace</div>
                            <div class="feature-desc">Interface optimisée</div>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="feature-text">
                            <div class="feature-title">Collaboration d'équipe</div>
                            <div class="feature-desc">Travaillez ensemble</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Section - Login Form -->
        <div class="right-section">
            <div class="login-container">
                <div class="login-header">
                    <div class="login-badge">
                        <i class="fas fa-star"></i>
                        Connexion sécurisée
                    </div>
                    <h2 class="login-title">Bon retour !</h2>
                    <p class="login-subtitle">
                        Connectez-vous à votre compte pour accéder à votre espace de gestion
                    </p>
                </div>
                
                <!-- CSRF Token -->
                @csrf
                
                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <span class="font-medium text-red-800">Erreurs de validation</span>
                        </div>
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Success Message -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span class="text-green-800">{{ session('status') }}</span>
                        </div>
                    </div>
                @endif
                
                <form class="login-form" method="POST" action="{{ route('login') }}">
                    <!-- CSRF Token -->
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="email">
                            Adresse email
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="email" 
                                id="email" 
                                name="email"
                                class="form-input @error('email') border-red-500 @enderror" 
                                placeholder="vous@exemple.com"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                autofocus
                            >
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">
                            Mot de passe
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                class="form-input @error('password') border-red-500 @enderror" 
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="far fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div class="form-options">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember" class="checkbox-label">
                                Se souvenir de moi
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">
                                Mot de passe oublié ?
                            </a>
                        @else
                            <a href="#" class="forgot-link" onclick="alert('Fonctionnalité de réinitialisation de mot de passe bientôt disponible!')">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <span>Se connecter</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
                
                <!-- <div class="divider">
                    <div class="divider-line"></div>
                    <span class="divider-text">Ou continuer avec</span>
                    <div class="divider-line"></div>
                </div>
                
                <div class="social-login">
                    <button class="social-btn">
                        <i class="fab fa-google"></i>
                        Google
                    </button>
                    <button class="social-btn">
                        <i class="fab fa-microsoft"></i>
                        Microsoft
                    </button>
                </div> -->
                
                <!-- <div class="signup-link">
                    Vous n'avez pas de compte ? 
                    <a href="#">Créer un compte</a>
                </div> -->
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Add focus animation to inputs
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>

    @livewireScripts
</body>
</html>
