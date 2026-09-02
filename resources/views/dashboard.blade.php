@extends('layouts.app')

@section('content')

{{-- Greeting --}}
<div class="mb-4">
    <h1 class="text-xl font-bold text-gray-800">
        Halo, {{ auth()->user()->name }}!
    </h1>
    <p class="text-gray-500 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
</div>

{{-- Status Absensi Hari Ini --}}
<div class="bg-white rounded-xl shadow p-4 mb-4">
    <div class="flex items-center justify-between gap-3">
        <div class="flex-1 min-w-0">
            <p class="text-xs text-gray-500">Status Absensi Hari Ini</p>
            @if($absensiHariIni)
                <p class="text-base font-bold mt-1 truncate
                    {{ $absensiHariIni->status == 'hadir' ? 'text-green-600' : '' }}
                    {{ $absensiHariIni->status == 'terlambat' ? 'text-yellow-600' : '' }}">
                    {{ ucfirst($absensiHariIni->status) }}
                    — Masuk {{ $absensiHariIni->jam_masuk }}
                    @if($absensiHariIni->jam_keluar)
                        | Keluar {{ $absensiHariIni->jam_keluar }}
                    @endif
                </p>
            @else
                <p class="text-base font-bold text-red-500 mt-1 flex items-center gap-1.5"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg> Belum Absen!</p>
            @endif
        </div>
    </div>
</div>

{{-- Statistik Pribadi Bulan Ini --}}
<div class="grid grid-cols-2 gap-3 mb-4">
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
        <p class="text-gray-500 text-xs">Hadir Bulan Ini</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ $totalHadir }}</p>
        <p class="text-xs text-gray-400">hari</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-xs">Terlambat</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $totalTerlambat }}</p>
        <p class="text-xs text-gray-400">kali</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
        <p class="text-gray-500 text-xs">Izin/Sakit</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalIzin }}</p>
        <p class="text-xs text-gray-400">hari</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 border-l-4 border-purple-500">
        <p class="text-gray-500 text-xs">Izin Pending</p>
        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $izinPending }}</p>
        <p class="text-xs text-gray-400">pengajuan</p>
    </div>
</div>

{{-- Statistik Kantor (khusus admin) --}}
@if(auth()->user()->role_id == 11)
<div class="grid grid-cols-1 gap-3 mb-4" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));">
    <div class="bg-indigo-600 rounded-xl shadow p-4 text-white">
        <p class="text-indigo-200 text-xs">Total Karyawan Aktif</p>
        <p class="text-2xl font-bold mt-1">{{ $totalKaryawan }}</p>
        <p class="text-indigo-200 text-xs mt-1">orang</p>
    </div>
    <div class="bg-green-600 rounded-xl shadow p-4 text-white">
        <p class="text-green-200 text-xs">Hadir Hari Ini</p>
        <p class="text-2xl font-bold mt-1">{{ $hadirHariIni }}</p>
        <p class="text-green-200 text-xs mt-1">karyawan</p>
    </div>
    <div class="rounded-xl shadow p-4 text-white flex items-center justify-between" style="background-color: #ea580c;">
        <div>
            <p class="text-orange-100 text-xs">Izin Menunggu Review</p>
            <p class="text-2xl font-bold mt-1">{{ $izinPendingAdmin }}</p>
            <p class="text-orange-100 text-xs mt-1">pengajuan</p>
        </div>
        @if($izinPendingAdmin > 0)
        <a href="{{ route('izin.review') }}"
           class="bg-white text-orange-500 hover:bg-orange-50 px-2 py-1 rounded-lg text-xs font-bold transition flex-shrink-0">
            Review
        </a>
        @endif
    </div>
</div>
@endif

{{-- Grafik & Kalender --}}
<div class="grid grid-cols-1 gap-4 mt-2" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">

    {{-- Grafik Kehadiran 7 Hari Terakhir --}}
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5V21h3.75v-7.5H3ZM10.125 9v12H13.5V9h-3.375ZM17.25 3v18H21V3h-3.75Z" /></svg> Kehadiran 7 Hari Terakhir</h2>
        <canvas id="grafikKehadiran" height="120"></canvas>
        <div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-500 border-t pt-3">
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-500 inline-block"></span> Hadir</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-400 inline-block"></span> Terlambat</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-400 inline-block"></span> Izin/Sakit</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-300 inline-block"></span> Alfa</span>
        </div>
    </div>

    {{-- Kalender --}}
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg> Kalender</h2>
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
#kalender .fc-toolbar { margin-bottom: 8px !important; }
#kalender .fc-toolbar-title { font-size: 0.85rem !important; font-weight: 600; color: #374151; }
#kalender .fc-button { padding: 2px 6px !important; font-size: 0.7rem !important; background: #4f46e5 !important; border-color: #4f46e5 !important; border-radius: 6px !important; }
#kalender .fc-button:hover { background: #4338ca !important; }
#kalender .fc-daygrid-day-number { font-size: 0.7rem !important; padding: 2px 3px !important; }
#kalender .fc-col-header-cell-cushion { font-size: 0.65rem !important; font-weight: 600; color: #6b7280; }
#kalender .fc-day-sun .fc-daygrid-day-number { color: #ef4444 !important; font-weight: bold; }
#kalender .fc-col-header-cell.fc-day-sun .fc-col-header-cell-cushion { color: #ef4444 !important; }
#kalender .fc-daygrid-day { min-height: 28px !important; }
#kalender .fc-scrollgrid { border-radius: 8px; overflow: hidden; }
#kalender .fc-today-button { background: #6b7280 !important; border-color: #6b7280 !important; }
#kalender .fc-day-today { background-color: #eef2ff !important; }
#kalender .fc-day-today .fc-daygrid-day-number {
    background-color: #4f46e5 !important;
    color: white !important;
    border-radius: 50% !important;
    width: 20px !important;
    height: 20px !important;
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
            tooltip: { callbacks: { label: (ctx) => ' ' + statusLabels[ctx.dataIndex] } }
        },
        scales: {
            y: { display: false, min: 0, max: 120 },
            x: { grid: { display: false } }
        }
    }
});

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

    const liburSet = new Set(hariLibur.map(h => h.date));
    const calendarEl = document.getElementById('kalender');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        firstDay: 0,
        headerToolbar: { left: 'prev,next', center: 'title', right: 'today' },
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
            if (day === 0 && num) { num.style.color = '#ef4444'; num.style.fontWeight = 'bold'; }
            if (liburSet.has(dateStr) && num) { num.style.color = '#ef4444'; num.style.fontWeight = 'bold'; }
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
                el.innerHTML = '<p style="font-weight:600;color:#374151;margin-bottom:6px;display:flex;align-items:center;gap:6px;"><svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg> Hari Libur Bulan Ini:</p>' +
                    libur.map(h => {
                        const d = new Date(h.date);
                        const tgl = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                        return `<div style="display:flex;gap:8px;"><span style="color:#ef4444;font-weight:600;min-width:44px;">${tgl}</span><span style="color:#6b7280;">: ${h.holiday_name}</span></div>`;
                    }).join('');
            } else {
                el.innerHTML = '<p style="color:#9ca3af;font-style:italic;">Tidak ada hari libur nasional bulan ini.</p>';
            }
        }
    });
    calendar.render();
}
initKalender();
</script>

@endsection