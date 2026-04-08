<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Sistem ERP Terintegrasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red: #dc2626;
            --red-dark: #b91c1c;
            --red-light: #fef2f2;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-400: #9ca3af;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--white);
            color: var(--gray-800);
            overflow-x: hidden;
        }

        /* NAV */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #f3f4f6;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-brand img {
            height: 36px;
        }

        .nav-brand span {
            font-size: 17px;
            font-weight: 800;
            color: var(--red);
            letter-spacing: -0.3px;
        }

        .nav-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-outline {
            padding: 8px 20px;
            border: 1.5px solid var(--red);
            color: var(--red);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-outline:hover {
            background: var(--red);
            color: white;
        }

        .btn-solid {
            padding: 8px 20px;
            background: var(--red);
            color: white;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-solid:hover { background: var(--red-dark); }

        /* HERO */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 80px 24px 60px;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -200px; right: -200px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(220,38,38,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -100px; left: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(220,38,38,0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr;
            gap: 48px;
            align-items: center;
            position: relative;
            z-index: 1;
            width: 100%;
        }

        @media (min-width: 900px) {
            .hero-inner { grid-template-columns: 1fr 1fr; }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--red-light);
            color: var(--red);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .hero-badge span { font-size: 14px; }

        .hero h1 {
            font-size: clamp(36px, 5vw, 56px);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1.5px;
            color: var(--gray-900);
            margin-bottom: 20px;
        }

        .hero h1 em {
            font-style: normal;
            color: var(--red);
        }

        .hero p {
            font-size: 17px;
            color: var(--gray-600);
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 480px;
        }

        .hero-cta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-hero {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background: var(--red);
            color: white;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 20px rgba(220,38,38,0.3);
        }

        .btn-hero:hover {
            background: var(--red-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(220,38,38,0.35);
        }

        /* Visual card */
        .hero-visual {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .visual-card {
            background: white;
            border-radius: 16px;
            padding: 20px 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            border: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 16px;
            transform: translateX(0);
            transition: transform 0.3s;
        }

        .visual-card:hover { transform: translateX(-4px); }

        .visual-card:nth-child(2) { margin-left: 24px; }
        .visual-card:nth-child(3) { margin-left: 48px; }

        .card-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .card-icon.red { background: #fee2e2; }
        .card-icon.orange { background: #ffedd5; }
        .card-icon.green { background: #dcfce7; }
        .card-icon.blue { background: #dbeafe; }

        .card-content p { font-size: 13px; color: var(--gray-400); }
        .card-content h4 { font-size: 15px; font-weight: 700; color: var(--gray-800); }

        .card-value {
            margin-left: auto;
            font-size: 20px;
            font-weight: 800;
            color: var(--red);
        }

        /* FEATURES */
        .features {
            padding: 80px 24px;
            background: var(--gray-50);
        }

        .section-header {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 56px;
        }

        .section-tag {
            display: inline-block;
            background: var(--red-light);
            color: var(--red);
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }

        .section-header h2 {
            font-size: clamp(28px, 4vw, 40px);
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--gray-900);
            margin-bottom: 14px;
        }

        .section-header p {
            font-size: 16px;
            color: var(--gray-600);
            line-height: 1.7;
        }

        .features-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 28px;
            border: 1px solid #f0f0f0;
            transition: all 0.3s;
        }

        .feature-card:hover {
            border-color: rgba(220,38,38,0.2);
            box-shadow: 0 8px 32px rgba(220,38,38,0.08);
            transform: translateY(-4px);
        }

        .feature-card .icon {
            width: 52px; height: 52px;
            background: var(--red-light);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 18px;
        }

        .feature-card h3 {
            font-size: 17px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 14px;
            color: var(--gray-600);
            line-height: 1.6;
        }

        /* STATS */
        .stats {
            padding: 80px 24px;
            background: var(--red);
            position: relative;
            overflow: hidden;
        }

        .stats::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 400px; height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .stats-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .stat-item h3 {
            font-size: 48px;
            font-weight: 800;
            color: white;
            letter-spacing: -2px;
        }

        .stat-item p {
            font-size: 15px;
            color: rgba(255,255,255,0.75);
            margin-top: 6px;
        }

        /* CTA */
        .cta-section {
            padding: 100px 24px;
            text-align: center;
        }

        .cta-section h2 {
            font-size: clamp(30px, 4vw, 44px);
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--gray-900);
            margin-bottom: 16px;
        }

        .cta-section p {
            font-size: 17px;
            color: var(--gray-600);
            margin-bottom: 36px;
        }

        /* FOOTER */
        footer {
            background: var(--gray-900);
            color: rgba(255,255,255,0.5);
            text-align: center;
            padding: 32px 24px;
            font-size: 13px;
        }

        footer strong { color: white; }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-text { animation: fadeUp 0.6s ease both; }
        .hero-badge { animation: fadeUp 0.5s ease both; }
        .hero h1 { animation: fadeUp 0.6s 0.1s ease both; }
        .hero p { animation: fadeUp 0.6s 0.2s ease both; }
        .hero-cta { animation: fadeUp 0.6s 0.3s ease both; }
        .visual-card:nth-child(1) { animation: fadeUp 0.6s 0.2s ease both; }
        .visual-card:nth-child(2) { animation: fadeUp 0.6s 0.3s ease both; }
        .visual-card:nth-child(3) { animation: fadeUp 0.6s 0.4s ease both; }
        .visual-card:nth-child(4) { animation: fadeUp 0.6s 0.5s ease both; }
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav>
        <div class="nav-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <span>{{ config('app.name') }}</span>
        </div>
        <div class="nav-actions">
            @auth
            <a href="{{ url('/dashboard') }}" class="btn-solid">Dashboard →</a>
            @else
            <a href="{{ route('login') }}" class="btn-solid">Masuk →</a>
            @endauth
        </div>
    </nav>

    {{-- HERO --}}
    <section class="hero">
        <div class="hero-inner">
            <div>
                <div class="hero-badge"><span>🚀</span> Sistem ERP Modern</div>
                <h1>Kelola Bisnis<br>Lebih <em>Cerdas</em><br>& Efisien.</h1>
                <p>Platform ERP terintegrasi untuk manajemen proyek, keuangan, gudang, SDM, dan absensi GPS dalam satu sistem yang mudah digunakan.</p>
                <div class="hero-cta">
                    @auth
                    <a href="{{ url('/dashboard') }}" class="btn-hero">Buka Dashboard →</a>
                    @else
                    <a href="{{ route('login') }}" class="btn-hero">Masuk Sekarang →</a>
                    @endauth
                </div>
            </div>

            <div class="hero-visual">
                <div class="visual-card">
                    <div class="card-icon red">📁</div>
                    <div class="card-content">
                        <p>Proyek Aktif</p>
                        <h4>Manajemen Proyek</h4>
                    </div>
                    <div class="card-value">✓</div>
                </div>
                <div class="visual-card">
                    <div class="card-icon green">💰</div>
                    <div class="card-content">
                        <p>Keuangan Real-time</p>
                        <h4>Laporan SO & PO</h4>
                    </div>
                    <div class="card-value">✓</div>
                </div>
                <div class="visual-card">
                    <div class="card-icon orange">🏭</div>
                    <div class="card-content">
                        <p>Stok Gudang</p>
                        <h4>FIFO & Serial Number</h4>
                    </div>
                    <div class="card-value">✓</div>
                </div>
                <div class="visual-card">
                    <div class="card-icon blue">📍</div>
                    <div class="card-content">
                        <p>Absensi Karyawan</p>
                        <h4>GPS + Foto Selfie</h4>
                    </div>
                    <div class="card-value">✓</div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES --}}
    <section class="features">
        <div class="section-header">
            <span class="section-tag">Fitur Lengkap</span>
            <h2>Semua yang Kamu Butuhkan</h2>
            <p>Dari absensi harian hingga laporan keuangan, semua tersedia dalam satu platform terintegrasi.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="icon">📊</div>
                <h3>Dashboard Informatif</h3>
                <p>Pantau statistik kehadiran, proyek aktif, dan laporan keuangan secara real-time.</p>
            </div>
            <div class="feature-card">
                <div class="icon">📍</div>
                <h3>Absensi GPS & Selfie</h3>
                <p>Karyawan absen dengan verifikasi lokasi GPS dan foto selfie untuk mencegah kecurangan.</p>
            </div>
            <div class="feature-card">
                <div class="icon">📁</div>
                <h3>Manajemen Proyek</h3>
                <p>Timeline otomatis 10 fase, milestone tracking, dan notifikasi deadline proyek.</p>
            </div>
            <div class="feature-card">
                <div class="icon">🏭</div>
                <h3>Gudang FIFO</h3>
                <p>Stok masuk/keluar dengan metode FIFO, serial number tracking, dan alert stok minimum.</p>
            </div>
            <div class="feature-card">
                <div class="icon">💰</div>
                <h3>Keuangan Terintegrasi</h3>
                <p>Sales Order, Purchase Order, faktur, pembayaran, dan laporan laba rugi dalam satu sistem.</p>
            </div>
            <div class="feature-card">
                <div class="icon">🏢</div>
                <h3>Multi Perusahaan</h3>
                <p>Kelola lebih dari satu PT dengan data yang terpisah dan karyawan yang bisa lintas PT.</p>
            </div>
        </div>
    </section>

    {{-- STATS --}}
    <section class="stats">
        <div class="stats-inner">
            <div class="stat-item">
                <h3>10+</h3>
                <p>Modul Terintegrasi</p>
            </div>
            <div class="stat-item">
                <h3>3+</h3>
                <p>Perusahaan Dikelola</p>
            </div>
            <div class="stat-item">
                <h3>100%</h3>
                <p>Berbasis Web & Mobile</p>
            </div>
            <div class="stat-item">
                <h3>24/7</h3>
                <p>Akses Kapan Saja</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta-section">
        <h2>Siap Memulai? 🚀</h2>
        <p>Masuk ke sistem dan kelola bisnis lebih efisien hari ini.</p>
        @auth
        <a href="{{ url('/dashboard') }}" class="btn-hero">Buka Dashboard →</a>
        @else
        <a href="{{ route('login') }}" class="btn-hero">Masuk Sekarang →</a>
        @endauth
    </section>

    {{-- FOOTER --}}
    <footer>
        © {{ date('Y') }} <strong>{{ config('app.name') }}</strong> · Sistem ERP Terintegrasi · Hak cipta dilindungi
    </footer>

</body>
</html>