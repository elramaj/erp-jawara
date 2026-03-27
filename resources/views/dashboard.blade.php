@extends('layouts.app')

@section('content')

{{-- Greeting --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        👋 Halo, {{ auth()->user()->name }}!
    </h1>
    <p class="text-gray-500 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
</div>

{{-- Status Absensi Hari Ini --}}
<div class="bg-white rounded-xl shadow p-5 mb-6 flex items-center justify-between">
    <div>
        <p class="text-sm text-gray-500">Status Absensi Hari Ini</p>
        @if($absensiHariIni)
            <p class="text-lg font-bold mt-1
                {{ $absensiHariIni->status == 'hadir' ? 'text-green-600' : '' }}
                {{ $absensiHariIni->status == 'terlambat' ? 'text-yellow-600' : '' }}">
                {{ ucfirst($absensiHariIni->status) }}
                — Masuk {{ $absensiHariIni->jam_masuk }}
                @if($absensiHariIni->jam_keluar)
                    | Keluar {{ $absensiHariIni->jam_keluar }}
                @endif
            </p>
        @else
            <p class="text-lg font-bold text-red-500 mt-1">⚠️ Belum Absen!</p>
        @endif
    </div>
    <a href="{{ route('absensi.index') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
        {{ $absensiHariIni ? 'Lihat Absensi' : 'Absen Sekarang' }}
    </a>
</div>

{{-- Statistik Pribadi Bulan Ini --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-gray-500 text-xs">Hadir Bulan Ini</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalHadir }}</p>
        <p class="text-xs text-gray-400 mt-1">hari</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-xs">Terlambat</p>
        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $totalTerlambat }}</p>
        <p class="text-xs text-gray-400 mt-1">kali</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
        <p class="text-gray-500 text-xs">Izin/Sakit</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalIzin }}</p>
        <p class="text-xs text-gray-400 mt-1">hari</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
        <p class="text-gray-500 text-xs">Izin Pending</p>
        <p class="text-3xl font-bold text-purple-600 mt-1">{{ $izinPending }}</p>
        <p class="text-xs text-gray-400 mt-1">pengajuan</p>
    </div>
</div>

{{-- Statistik Kantor (khusus admin) --}}
@if(auth()->user()->role_id == 11)
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-indigo-600 rounded-xl shadow p-5 text-white">
        <p class="text-indigo-200 text-xs">Total Karyawan Aktif</p>
        <p class="text-3xl font-bold mt-1">{{ $totalKaryawan }}</p>
        <p class="text-indigo-200 text-xs mt-1">orang</p>
    </div>
    <div class="bg-green-600 rounded-xl shadow p-5 text-white">
        <p class="text-green-200 text-xs">Hadir Hari Ini</p>
        <p class="text-3xl font-bold mt-1">{{ $hadirHariIni }}</p>
        <p class="text-green-200 text-xs mt-1">karyawan</p>
    </div>
    <div class="bg-orange-500 rounded-xl shadow p-5 text-white flex items-center justify-between">
        <div>
            <p class="text-orange-100 text-xs">Izin Menunggu Review</p>
            <p class="text-3xl font-bold mt-1">{{ $izinPendingAdmin }}</p>
            <p class="text-orange-100 text-xs mt-1">pengajuan</p>
        </div>
        @if($izinPendingAdmin > 0)
        <a href="{{ route('izin.review') }}"
           class="bg-white text-orange-500 hover:bg-orange-50 px-3 py-1.5 rounded-lg text-xs font-bold transition">
            Review
        </a>
        @endif
    </div>
</div>
@endif

{{-- Grafik & Kalender --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    {{-- Grafik Kehadiran 7 Hari Terakhir --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">📊 Kehadiran 7 Hari Terakhir</h2>
        <canvas id="grafikKehadiran" height="120"></canvas>
        <div class="flex gap-4 mt-4 text-xs text-gray-500 border-t pt-3">
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-500 inline-block"></span> Hadir</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-400 inline-block"></span> Terlambat</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-400 inline-block"></span> Izin/Sakit</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-300 inline-block"></span> Alfa</span>
        </div>
    </div>

    {{-- Kalender --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-3">📅 Kalender</h2>
        <div id="kalender"></div>
        <div id="keterangan-libur" class="mt-3 border-t pt-3 text-xs text-gray-600 space-y-1"></div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
{{-- FullCalendar --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.js"></script>

<style>
/* Kalender styling */
#kalender .fc-toolbar { margin-bottom: 8px !important; }
#kalender .fc-toolbar-title { font-size: 0.95rem !important; font-weight: 600; color: #374151; }
#kalender .fc-button { padding: 2px 8px !important; font-size: 0.75rem !important; background: #4f46e5 !important; border-color: #4f46e5 !important; border-radius: 6px !important; }
#kalender .fc-button:hover { background: #4338ca !important; }
#kalender .fc-daygrid-day-number { font-size: 0.75rem !important; padding: 2px 4px !important; }
#kalender .fc-col-header-cell-cushion { font-size: 0.7rem !important; font-weight: 600; color: #6b7280; }
#kalender .fc-day-sun .fc-daygrid-day-number { color: #ef4444 !important; font-weight: bold; }
#kalender .fc-col-header-cell.fc-day-sun .fc-col-header-cell-cushion { color: #ef4444 !important; }
#kalender .fc-daygrid-day { min-height: 32px !important; }
#kalender .fc-scrollgrid { border-radius: 8px; overflow: hidden; }
#kalender .fc-today-button { background: #6b7280 !important; border-color: #6b7280 !important; }

/* Highlight hari ini */
#kalender .fc-day-today {
    background-color: #eef2ff !important;
}
#kalender .fc-day-today .fc-daygrid-day-number {
    background-color: #4f46e5 !important;
    color: white !important;
    border-radius: 50% !important;
    width: 22px !important;
    height: 22px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: bold !important;
}
</style>

<script>
const grafikData = @json($grafik);

const labels = grafikData.map(g => g.hari);
const colors = grafikData.map(g => {
    if (g.status === 'hadir')     return '#22c55e';
    if (g.status === 'terlambat') return '#facc15';
    if (g.status === 'izin' || g.status === 'sakit') return '#60a5fa';
    return '#fca5a5';
});
const values = grafikData.map(g => {
    if (g.status === 'hadir')     return 100;
    if (g.status === 'terlambat') return 75;
    if (g.status === 'izin' || g.status === 'sakit') return 50;
    return 25;
});
const statusLabels = grafikData.map(g => {
    if (g.status === 'hadir')     return 'Hadir';
    if (g.status === 'terlambat') return 'Terlambat';
    if (g.status === 'izin')      return 'Izin';
    if (g.status === 'sakit')     return 'Sakit';
    return 'Alfa';
});

new Chart(document.getElementById('grafikKehadiran'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Status Kehadiran',
            data: values,
            backgroundColor: colors,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (ctx) => ' ' + statusLabels[ctx.dataIndex]
                }
            }
        },
        scales: {
            y: { display: false, min: 0, max: 120 },
            x: { grid: { display: false } }
        }
    }
});

// Kalender dengan data hari libur nasional 2026 (resmi pemerintah)
function initKalender() {
    const hariLibur = [
    { date: '2026-01-01', holiday_name: 'Tahun Baru 2026 Masehi' },
    { date: '2026-01-16', holiday_name: 'Isra Mikraj Nabi Muhammad SAW' },
    { date: '2026-02-16', holiday_name: 'Cuti Tahun Baru Imlek 2577 Kongzili' },
    { date: '2026-02-17', holiday_name: 'Tahun Baru Imlek 2577 Kongzili' },
    { date: '2026-03-18', holiday_name: 'Cuti Hari Suci Nyepi Tahun Baru Saka 1948' },
    { date: '2026-03-19', holiday_name: 'Hari Suci Nyepi Tahun Baru Saka 1948' },
    { date: '2026-03-20', holiday_name: 'Cuti Hari Raya Idulfitri 1447 Hijriah' },
    { date: '2026-03-21', holiday_name: 'Hari Raya Idulfitri 1447 Hijriah' },
    { date: '2026-03-22', holiday_name: 'Hari Raya Idulfitri 1447 Hijriah' },
    { date: '2026-03-23', holiday_name: 'Cuti Hari Raya Idulfitri 1447 Hijriah' },
    { date: '2026-03-24', holiday_name: 'Cuti Hari Raya Idulfitri 1447 Hijriah' },
    { date: '2026-04-03', holiday_name: 'Wafat Yesus Kristus' },
    { date: '2026-04-05', holiday_name: 'Hari Paskah' },
    { date: '2026-05-01', holiday_name: 'Hari Buruh Internasional' },
    { date: '2026-05-14', holiday_name: 'Kenaikan Yesus Kristus' },
    { date: '2026-05-15', holiday_name: 'Cuti Kenaikan Yesus Kristus' },
    { date: '2026-05-27', holiday_name: 'Hari Raya Iduladha 1447 Hijriah' },
    { date: '2026-05-28', holiday_name: 'Cuti Hari Raya Iduladha 1447 Hijriah' },
    { date: '2026-05-31', holiday_name: 'Hari Raya Waisak 2570' },
    { date: '2026-06-01', holiday_name: 'Hari Lahir Pancasila' },
    { date: '2026-06-16', holiday_name: 'Tahun Baru Islam 1448 Hijriah' },
    { date: '2026-08-17', holiday_name: 'Hari Kemerdekaan RI' },
    { date: '2026-08-25', holiday_name: 'Maulid Nabi Muhammad SAW' },
    { date: '2026-12-24', holiday_name: 'Cuti Hari Raya Natal' },
    { date: '2026-12-25', holiday_name: 'Hari Raya Natal' },
];

    // Set tanggal libur sebagai background merah
    const liburSet = new Set(hariLibur.map(h => h.date));

    const calendarEl = document.getElementById('kalender');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        firstDay: 0,
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'today'
        },
        height: 'auto',
        fixedWeekCount: false,
        events: hariLibur.map(h => ({
            date: h.date,
            display: 'background',
            backgroundColor: '#fee2e2',
        })),
        dayCellDidMount: function(info) {
            const day = info.date.getDay();
            const dateStr = info.date.toISOString().split('T')[0];
            const num = info.el.querySelector('.fc-daygrid-day-number');

            // Warnai angka Minggu merah
            if (day === 0 && num) {
                num.style.color = '#ef4444';
                num.style.fontWeight = 'bold';
            }

            // Warnai angka hari libur merah
            if (liburSet.has(dateStr) && num) {
                num.style.color = '#ef4444';
                num.style.fontWeight = 'bold';
            }
        },
        datesSet: function() {
            const bulanAktif = calendar.getDate().getMonth();
            const tahunAktif = calendar.getDate().getFullYear();
            const libur = hariLibur.filter(h => {
                const d = new Date(h.date);
                return d.getMonth() === bulanAktif && d.getFullYear() === tahunAktif;
            });

            const el = document.getElementById('keterangan-libur');
            if (libur.length > 0) {
                el.innerHTML = '<p class="font-semibold text-gray-700 mb-2">🗓️ Hari Libur Bulan Ini:</p>' +
                    libur.map(h => {
                        const d = new Date(h.date);
                        const tgl = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                        return `<div class="flex gap-2"><span class="text-red-500 font-semibold w-12">${tgl}</span><span class="text-gray-500">: ${h.holiday_name}</span></div>`;
                    }).join('');
            } else {
                el.innerHTML = '<p class="text-gray-400 italic">Tidak ada hari libur nasional bulan ini.</p>';
            }
        }
    });

    calendar.render();
}

initKalender();
</script>

@endsection