@extends('layouts.app')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Minta Dokumen</h1>

    @if($errors->any())
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4 border border-red-300"><ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('dokumen.store') }}" class="bg-white rounded-xl shadow p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Dokumen *</label>
            <input type="text" name="jenis_dokumen" list="daftar-jenis-dok" value="{{ old('jenis_dokumen') }}" maxlength="150" required placeholder="Pilih atau ketik: NIB, BAST, Kontrak..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <datalist id="daftar-jenis-dok">@foreach(\App\Models\PermintaanDokumen::JENIS_SARAN as $j)<option value="{{ $j }}">@endforeach</datalist>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan *</label>
            <textarea name="keperluan" rows="4" maxlength="2000" required placeholder="Untuk apa dokumen ini, ke siapa diserahkan, detail yang perlu dicantumkan..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('keperluan') }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline') }}" min="{{ date('Y-m-d') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Proyek (opsional)</label>
                <select name="proyek_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="">— tanpa proyek —</option>
                    @foreach($proyek as $p)
                    <option value="{{ $p->id }}" {{ old('proyek_id') == $p->id ? 'selected' : '' }}>{{ $p->kode_proyek }} · {{ $p->nama_proyek }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">Kirim Permintaan</button>
            <a href="{{ route('dokumen.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
        </div>
    </form>
</div>
@endsection
