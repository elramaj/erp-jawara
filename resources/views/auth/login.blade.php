<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red: #dc2626;
            --red-dark: #b91c1c;
            --red-light: #fee2e2;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-400: #9ca3af;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--gray-50);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        /* Left panel */
        .left-panel {
            display: none;
            flex: 1;
            background: var(--red);
            position: relative;
            overflow: hidden;
            padding: 48px;
            flex-direction: column;
            justify-content: space-between;
        }

        @media (min-width: 1024px) {
            .left-panel { display: flex; }
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 400px; height: 400px;
            background: rgba(255,255,255,0.07);
            border-radius: 50%;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 300px; height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .left-panel .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .left-panel .brand img {
            height: 100px;
            filter: brightness(0) invert(1);
        }

        .left-panel .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }

        .left-panel .hero-text {
            position: relative;
            z-index: 1;
        }

        .left-panel .hero-text h1 {
            font-size: 42px;
            font-weight: 800;
            color: white;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 16px;
        }

        .left-panel .hero-text p {
            font-size: 16px;
            color: rgba(255,255,255,0.75);
            line-height: 1.6;
            max-width: 360px;
        }

        .left-panel .features {
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: relative;
            z-index: 1;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .feature-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .feature-text {
            font-size: 14px;
            color: rgba(255,255,255,0.85);
            font-weight: 500;
        }

        /* Right panel / login form */
        .right-panel {
            width: 100%;
            max-width: 480px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 40px;
            position: relative;
        }

        @media (min-width: 1024px) {
            .right-panel { min-width: 440px; }
        }

        @media (max-width: 480px) {
            .right-panel { padding: 40px 24px; }
        }

        .mobile-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        @media (min-width: 1024px) {
            .mobile-logo { display: none; }
        }

        .mobile-logo img {
            height: 40px;
        }

        .mobile-logo span {
            font-size: 18px;
            font-weight: 800;
            color: var(--red);
        }

        .login-header {
            margin-bottom: 36px;
        }

        .login-header h2 {
            font-size: 28px;
            font-weight: 800;
            color: var(--gray-800);
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .login-header p {
            font-size: 14px;
            color: var(--gray-400);
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--gray-800);
            background: var(--gray-50);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            background: white;
        }

        .form-input.error {
            border-color: var(--red);
        }

        .error-msg {
            font-size: 12px;
            color: var(--red);
            margin-top: 6px;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: var(--gray-600);
        }

        .remember-label input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--red);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 13px;
            color: var(--red);
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--red);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.2px;
        }

        .btn-login:hover { background: var(--red-dark); }
        .btn-login:active { transform: scale(0.99); }

        .login-footer {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid var(--gray-100);
            text-align: center;
            font-size: 12px;
            color: var(--gray-400);
        }

        /* Decorative dots */
        .dots {
            position: absolute;
            bottom: 24px;
            right: 24px;
            display: grid;
            grid-template-columns: repeat(4, 6px);
            gap: 4px;
        }

        .dots span {
            width: 6px; height: 6px;
            background: var(--red-light);
            border-radius: 50%;
            display: block;
        }

        /* Animate in */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .right-panel > * {
            animation: fadeUp 0.5s ease both;
        }

        .right-panel .mobile-logo { animation-delay: 0.05s; }
        .right-panel .login-header { animation-delay: 0.1s; }
        .right-panel form { animation-delay: 0.15s; }
    </style>
</head>
<body>

    {{-- Left Panel --}}
    <div class="left-panel">
    <div class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
    </div>

        <div class="hero-text">
            <h1>Kelola Bisnis<br>Lebih Cerdas.</h1>
            <p>Sistem ERP terintegrasi untuk manajemen proyek, keuangan, gudang, dan SDM dalam satu platform.</p>
        </div>

        <div class="features">
            <div class="feature-item">
                <div class="feature-icon"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-19.5 0v6a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25v-6m-19.5 0V6a2.25 2.25 0 0 1 2.25-2.25h5.379a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H19.5A2.25 2.25 0 0 1 21.75 9v3.75" /></svg></div>
                <span class="feature-text">Manajemen Proyek & Timeline</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg></div>
                <span class="feature-text">Laporan Keuangan Real-time</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 21v-4.5m0 4.5h3.75m-3.75 0h-9m9-4.5V9m0 7.5H12m3.75-7.5V4.875c0-.621-.504-1.125-1.125-1.125h-3.75c-.621 0-1.125.504-1.125 1.125V9m4.5 0H12m0 0V4.875c0-.621-.504-1.125-1.125-1.125h-3.75c-.621 0-1.125.504-1.125 1.125V9m4.5 0H8.25M12 9v7.5m0-7.5H8.25m4.5 7.5H8.25m0-7.5V21m0-11.625v11.625m0 0H4.875c-.621 0-1.125-.504-1.125-1.125V9.375c0-.621.504-1.125 1.125-1.125H8.25" /></svg></div>
                <span class="feature-text">Stok Gudang dengan FIFO</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg></div>
                <span class="feature-text">Absensi GPS + Foto Selfie</span>
            </div>
        </div>
    </div>

    {{-- Right Panel (Login Form) --}}
    <div class="right-panel">

        {{-- Mobile logo --}}
        <div class="mobile-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <span>{{ config('app.name') }}</span>
        </div>

        <div class="login-header">
            <h2>Selamat Datang</h2>
            <p>Masuk ke akun kamu untuk melanjutkan</p>
        </div>

        {{-- Session status --}}
        @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
        <div class="alert-error flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input id="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                    type="email" name="email" value="{{ old('email') }}"
                    required autofocus autocomplete="username"
                    placeholder="nama@perusahaan.com">
                @error('email')
                <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div style="position:relative;">
    <input id="password" class="form-input {{ $errors->has('password') ? 'error' : '' }}"
        type="password" name="password"
        required autocomplete="current-password"
        placeholder="••••••••"
        style="padding-right: 44px;">
    <button type="button" onclick="togglePassword()"
        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;">
        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
        </svg>
    </button>
</div>
                @error('password')
                <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-row">
                <label class="remember-label">
                    <input type="checkbox" name="remember">
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-login">Masuk →</button>
        </form>

        <div class="login-footer">
            © {{ date('Y') }} {{ config('app.name') }} · Hak cipta dilindungi
        </div>

        {{-- Decorative dots --}}
        <div class="dots">
            @for($i = 0; $i < 16; $i++)
            <span></span>
            @endfor
        </div>
    </div>

    <script>
function togglePassword() {
    const input = document.getElementById('password');
    const eyeOn = document.getElementById('eye-icon');
    const eyeOff = document.getElementById('eye-off-icon');
    if (input.type === 'password') {
        input.type = 'text';
        eyeOn.style.display = 'none';
        eyeOff.style.display = 'block';
    } else {
        input.type = 'password';
        eyeOn.style.display = 'block';
        eyeOff.style.display = 'none';
    }
}
</script>

</body>
</html>