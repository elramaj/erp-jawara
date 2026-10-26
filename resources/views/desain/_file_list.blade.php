{{-- Param: $files (koleksi DesainFile), $desain --}}
<ul class="divide-y divide-gray-100 text-sm">
    @foreach($files as $f)
    <li class="flex items-center justify-between gap-3 py-2">
        <div class="min-w-0">
            <p class="text-gray-800 truncate">{{ $f->nama_asli }}</p>
            <p class="text-xs text-gray-400">{{ \App\Support\BerkasPrivat::ukuranLabel((int) $f->ukuran) }} · {{ $f->uploader->name ?? '-' }} · {{ $f->created_at->format('d M Y H:i') }}</p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            @if(in_array($f->mime, ['image/png','image/jpeg','image/webp','image/gif']))
            <a href="{{ route('desain.file', [$desain, $f]) }}" target="_blank" class="text-xs text-indigo-600 hover:underline font-semibold">Lihat</a>
            @endif
            <a href="{{ route('desain.file', [$desain, $f, 'unduh' => 1]) }}" class="text-xs text-gray-600 hover:underline font-semibold">Unduh</a>
        </div>
    </li>
    @endforeach
</ul>
