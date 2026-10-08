{{-- Param: $kolom = [['Dibuat oleh', 'Nama'], ['Disetujui', ''], ...] --}}
<table class="ttd">
    <tr>
        @foreach($kolom as [$judulKolom, $namaKolom])
        <td>
            <div>{{ $judulKolom }}</div>
            <div class="ruang"></div>
            <div class="nama">{{ $namaKolom ?: '(............................)' }}</div>
        </td>
        @endforeach
    </tr>
</table>
