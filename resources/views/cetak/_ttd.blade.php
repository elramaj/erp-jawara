{{-- Param: $kolom = [['Dibuat oleh', 'Nama', 'Jabatan (opsional)'], ['Disetujui', ''], ...] --}}
<table class="ttd">
    <tr>
        @foreach($kolom as $k)
        <td>
            <div>{{ $k[0] }}</div>
            <div class="ruang"></div>
            <div class="nama">{{ ($k[1] ?? '') ?: '(............................)' }}</div>
            @if(!empty($k[2]))<div class="jabatan">{{ $k[2] }}</div>@endif
        </td>
        @endforeach
    </tr>
</table>
