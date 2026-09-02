<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Absensi Mobile</title>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; margin: 0; padding: 0; }
        video { width: 100%; border-radius: 12px; display: block; background: #000; min-height: 200px; }
        canvas { display: none; }
        .card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); padding: 16px; margin-bottom: 12px; }
        .header { background: #4338ca; color: white; padding: 16px; display: flex; justify-content: space-between; align-items: center; }
        .badge-dalam { background: #dcfce7; border: 1px solid #86efac; border-radius: 12px; padding: 12px; margin-bottom: 12px; }
        .badge-luar { background: #fff7ed; border: 1px solid #fdba74; border-radius: 12px; padding: 12px; margin-bottom: 12px; }
        .badge-loading { background: #eff6ff; border: 1px solid #93c5fd; border-radius: 12px; padding: 12px; margin-bottom: 12px; }
        .badge-error { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 12px; padding: 12px; margin-bottom: 12px; }
        .btn-green { width: 100%; background: #16a34a; color: white; padding: 16px; border-radius: 14px; font-size: 16px; font-weight: 700; border: none; cursor: pointer; }
        .btn-green:disabled { background: #d1d5db; cursor: not-allowed; }
        .btn-blue { width: 100%; background: #2563eb; color: white; padding: 16px; border-radius: 14px; font-size: 16px; font-weight: 700; border: none; cursor: pointer; }
        .btn-blue:disabled { background: #d1d5db; cursor: not-allowed; }
        .btn-indigo { background: #4338ca; color: white; padding: 10px 16px; border-radius: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; flex: 1; }
        .btn-gray { background: #f3f4f6; color: #374151; padding: 10px 16px; border-radius: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 12px; }
        .stat-box { border-radius: 10px; padding: 12px; text-align: center; }
        .stat-green { background: #f0fdf4; }
        .stat-blue { background: #eff6ff; }
        .foto-preview { width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 4px solid #818cf8; display: block; margin: 8px auto 0; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="absensiApp()" x-init="init()">

    {{-- Header --}}
    <div class="header">
        <div>
            <p style="font-weight:700;font-size:18px;margin:0;"><svg style="width:18px;height:18px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Absensi</p>
            <p style="font-size:12px;opacity:0.8;margin:0;">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <div style="text-align:right;">
            <p style="font-weight:600;font-size:14px;margin:0;">{{ auth()->user()->name }}</p>
            <p style="font-size:12px;opacity:0.8;margin:0;">{{ auth()->user()->role->name ?? '-' }}</p>
        </div>
    </div>

    <div style="padding:16px;">

        @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:12px;margin-bottom:12px;color:#166534;font-size:14px;">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:12px;padding:12px;margin-bottom:12px;color:#991b1b;font-size:14px;">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg> {{ session('error') }}
        </div>
        @endif

        {{-- Status Absensi Hari Ini --}}
        @if($absensiHariIni)
        <div class="card">
            <p style="font-size:12px;color:#9ca3af;margin:0 0 4px;">Status Hari Ini</p>
            <p style="font-size:18px;font-weight:700;margin:0;color:{{ $absensiHariIni->status == 'hadir' ? '#16a34a' : ($absensiHariIni->status == 'terlambat' ? '#d97706' : '#374151') }}">
                {{ ucfirst($absensiHariIni->status) }}
            </p>
            <div class="grid-2">
                <div class="stat-box stat-green">
                    <p style="font-size:11px;color:#9ca3af;margin:0;">Masuk</p>
                    <p style="font-weight:700;color:#16a34a;margin:4px 0;">{{ $absensiHariIni->jam_masuk ?? '-' }}</p>
                    @if($absensiHariIni->foto_masuk)
                    <img src="{{ Storage::url($absensiHariIni->foto_masuk) }}" class="foto-preview" style="width:48px;height:48px;margin:6px auto 0;">
                    @endif
                    @if($absensiHariIni->lokasi_valid == 1)
                    <p style="font-size:11px;color:#16a34a;margin:4px 0 0;"><svg style="width:14px;height:14px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Dalam area</p>
                    @elseif($absensiHariIni->lokasi_valid == 0)
                    <p style="font-size:11px;color:#d97706;margin:4px 0 0;"><svg style="width:14px;height:14px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>Di luar area</p>
                    @endif
                </div>
                <div class="stat-box stat-blue">
                    <p style="font-size:11px;color:#9ca3af;margin:0;">Keluar</p>
                    <p style="font-weight:700;color:#2563eb;margin:4px 0;">{{ $absensiHariIni->jam_keluar ?? '-' }}</p>
                    @if($absensiHariIni->foto_keluar)
                    <img src="{{ Storage::url($absensiHariIni->foto_keluar) }}" class="foto-preview" style="width:48px;height:48px;margin:6px auto 0;">
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Alert Lokasi --}}
        <div x-show="lokasiStatus == 'loading'" class="badge-loading" x-cloak>
            <p style="font-size:14px;color:#1d4ed8;margin:0;"><svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>Mendeteksi lokasi kamu...</p>
        </div>
        <div x-show="lokasiStatus == 'dalam'" class="badge-dalam" x-cloak>
            <p style="font-size:14px;font-weight:600;color:#166534;margin:0;"><svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>Kamu dalam area kantor</p>
            <p style="font-size:12px;color:#15803d;margin:4px 0 0;">Lokasi terdeteksi valid</p>
        </div>
        <div x-show="lokasiStatus == 'luar'" class="badge-luar" x-cloak>
            <p style="font-size:14px;font-weight:600;color:#9a3412;margin:0;"><svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>Kamu di luar area kantor</p>
            <p style="font-size:12px;color:#c2410c;margin:4px 0 0;">Absensi tetap bisa dilakukan sebagai WFH / Dinas Luar</p>
        </div>
        <div x-show="lokasiStatus == 'error'" class="badge-error" x-cloak>
            <p style="font-size:14px;color:#991b1b;margin:0;"><svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>GPS tidak dapat diakses. Aktifkan izin lokasi di browser.</p>
        </div>

        {{-- Kamera --}}
        @if(!($absensiHariIni && $absensiHariIni->jam_keluar))
        <div class="card" x-show="showCamera">
            <p style="font-size:14px;font-weight:600;color:#374151;margin:0 0 12px;"><svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" /></svg>Foto Selfie</p>
            <video id="video" autoplay playsinline></video>
            <canvas id="canvas"></canvas>

            <div x-show="fotoPreview" style="margin-top:12px;text-align:center;" x-cloak>
                <p style="font-size:12px;color:#9ca3af;margin:0 0 4px;">Preview:</p>
                <img :src="fotoPreview" class="foto-preview">
            </div>

            <div style="display:flex;gap:8px;margin-top:12px;">
                <button class="btn-indigo" @click="ambilFoto()"><svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" /></svg>Ambil Foto</button>
                <button class="btn-gray" @click="resetFoto()" x-show="fotoPreview" x-cloak><svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>Ulang</button>
            </div>
        </div>

        <div class="card" x-show="!showCamera" x-cloak>
            <p style="font-size:14px;color:#ef4444;margin:0;"><svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" /></svg>Kamera tidak dapat diakses.</p>
            <p style="font-size:12px;color:#6b7280;margin:4px 0 0;">Pastikan izin kamera sudah diaktifkan di browser, lalu refresh halaman.</p>
        </div>
        @endif

        {{-- Tombol Absen --}}
        @if(!$absensiHariIni)
        <form method="POST" action="{{ route('absensi.checkin.mobile') }}" id="form-checkin">
            @csrf
            <input type="hidden" name="lat" id="input-lat">
            <input type="hidden" name="lng" id="input-lng">
            <input type="hidden" name="lokasi_valid" id="input-valid">
            <input type="hidden" name="foto" id="input-foto">
            <button type="button" class="btn-green"
                @click="prosesAbsen('checkin')"
                :disabled="!fotoPreview || lokasiStatus == 'loading'">
                <svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>Check In Sekarang
            </button>
        </form>

        @elseif(!$absensiHariIni->jam_keluar)
        <form method="POST" action="{{ route('absensi.checkout.mobile') }}" id="form-checkout">
            @csrf
            <input type="hidden" name="lat" id="input-lat-out">
            <input type="hidden" name="lng" id="input-lng-out">
            <input type="hidden" name="foto" id="input-foto-out">
            <button type="button" class="btn-blue"
                @click="prosesAbsen('checkout')"
                :disabled="!fotoPreview || lokasiStatus == 'loading'">
                <svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" /></svg>Check Out Sekarang
            </button>
        </form>

        @else
        <div style="background:#f3f4f6;border-radius:14px;padding:16px;text-align:center;color:#6b7280;font-size:14px;">
            <svg style="width:16px;height:16px;display:inline-block;vertical-align:-3px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>Absensi hari ini sudah lengkap!
        </div>
        @endif

        <div style="margin-top:16px;text-align:center;">
            <a href="{{ route('dashboard') }}" style="color:#6366f1;font-size:14px;text-decoration:none;display:inline-flex;align-items:center;gap:4px;"><svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg> Kembali ke Dashboard</a>
        </div>
    </div>

<script>
function absensiApp() {
    return {
        lokasiStatus: 'loading',
        lat: null,
        lng: null,
        lokasiValid: false,
        showCamera: true,
        fotoPreview: null,
        stream: null,

        init() {
            this.startCamera();
            this.getLocation();
        },

        startCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                this.showCamera = false;
                return;
            }
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
                .then(stream => {
                    this.stream = stream;
                    const video = document.getElementById('video');
                    video.srcObject = stream;
                    video.play();
                })
                .catch(err => {
                    console.log('Kamera error:', err);
                    this.showCamera = false;
                });
        },

        getLocation() {
            if (!navigator.geolocation) {
                this.lokasiStatus = 'error';
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.lat = pos.coords.latitude;
                    this.lng = pos.coords.longitude;
                    this.cekRadius();
                },
                (err) => {
                    console.log('GPS error:', err);
                    this.lokasiStatus = 'error';
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        },

        cekRadius() {
            const kantorLat = {{ $lokasiKantor->latitude ?? -7.2575 }};
            const kantorLng = {{ $lokasiKantor->longitude ?? 112.7521 }};
            const radius    = {{ $lokasiKantor->radius_meter ?? 100 }};

            const R    = 6371000;
            const dLat = (this.lat - kantorLat) * Math.PI / 180;
            const dLng = (this.lng - kantorLng) * Math.PI / 180;
            const a    = Math.sin(dLat/2) * Math.sin(dLat/2) +
                         Math.cos(kantorLat * Math.PI / 180) * Math.cos(this.lat * Math.PI / 180) *
                         Math.sin(dLng/2) * Math.sin(dLng/2);
            const jarak = R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

            this.lokasiValid  = jarak <= radius;
            this.lokasiStatus = this.lokasiValid ? 'dalam' : 'luar';
        },

        ambilFoto() {
            const video  = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            if (!video.videoWidth) {
                alert('Kamera belum siap, coba lagi!');
                return;
            }
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            this.fotoPreview = canvas.toDataURL('image/jpeg', 0.8);
        },

        resetFoto() {
            this.fotoPreview = null;
        },

        prosesAbsen(tipe) {
            if (!this.fotoPreview) {
                alert('Harap ambil foto selfie terlebih dahulu!');
                return;
            }
            if (this.lokasiStatus === 'loading') {
                alert('Tunggu GPS selesai mendeteksi lokasi!');
                return;
            }

            const formId = tipe === 'checkin' ? 'form-checkin' : 'form-checkout';
            const suffix = tipe === 'checkin' ? '' : '-out';

            document.getElementById('input-lat' + suffix).value  = this.lat ?? '';
            document.getElementById('input-lng' + suffix).value  = this.lng ?? '';
            if (tipe === 'checkin') {
                document.getElementById('input-valid').value = this.lokasiValid ? 1 : 0;
            }
            document.getElementById('input-foto' + suffix).value = this.fotoPreview;

            document.getElementById(formId).submit();
        }
    }
}
</script>
</body>
</html>