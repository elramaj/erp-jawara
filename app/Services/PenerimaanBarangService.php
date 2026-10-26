<?php

namespace App\Services;

use App\Models\GudangSerialNumber;
use App\Models\GudangStokMasuk;
use App\Models\Po;
use App\Models\PoDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Pencatatan barang datang dari PO. Dipakai bersama oleh halaman PO (Keuangan/PO)
 * dan halaman Penerimaan Barang (Gudang), supaya hasilnya selalu sama:
 * jumlah_diterima PO naik, stok gudang bertambah, status PO ikut berubah.
 */
class PenerimaanBarangService
{
    /** Status PO yang tidak boleh lagi menerima barang. */
    private const STATUS_TERTUTUP = ['selesai', 'batal', 'draft'];

    /**
     * @param  array<int, array{barang_id:int|string, jumlah:int|string, serial_numbers?:?string}>  $baris
     * @return int jumlah baris yang tercatat
     * @throws ValidationException
     */
    public static function catat(Po $po, string $tanggal, array $baris, ?int $userId): int
    {
        if (in_array($po->status, self::STATUS_TERTUTUP, true)) {
            throw ValidationException::withMessages(['jumlah' => 'PO ini berstatus "' . $po->status . '", tidak bisa menerima barang.']);
        }

        return DB::transaction(function () use ($po, $tanggal, $baris, $userId) {
            // kunci baris PO supaya dua orang yang menyimpan bersamaan tidak melewati batas sisa
            $detail = PoDetail::where('po_id', $po->id)->lockForUpdate()->get()->keyBy('barang_id');
            $dicatat = 0;

            foreach ($baris as $b) {
                $jumlah = (int) ($b['jumlah'] ?? 0);
                if ($jumlah <= 0) {
                    continue;
                }

                $d = $detail->get((int) ($b['barang_id'] ?? 0));
                if (!$d) {
                    throw ValidationException::withMessages(['barang_id' => 'Ada barang yang tidak termasuk dalam PO ini.']);
                }

                $sisa = (int) $d->jumlah - (int) $d->jumlah_diterima;
                $nama = optional($d->barang)->nama_barang ?? 'barang';
                if ($jumlah > $sisa) {
                    throw ValidationException::withMessages(['jumlah' => "Jumlah {$nama} ({$jumlah}) melebihi sisa PO ({$sisa})."]);
                }

                $sns = self::parseSerial($b['serial_numbers'] ?? null);
                if ($sns && count($sns) !== $jumlah) {
                    throw ValidationException::withMessages(['jumlah' => "Serial number {$nama} berjumlah " . count($sns) . ", harus sama dengan jumlah datang ({$jumlah})."]);
                }

                $d->increment('jumlah_diterima', $jumlah);

                $masuk = GudangStokMasuk::create([
                    'barang_id'  => $d->barang_id,
                    'tanggal'    => $tanggal,
                    'jumlah'     => $jumlah,
                    'sisa'       => $jumlah,
                    'supplier'   => optional($po->supplier)->nama,
                    'no_dokumen' => $po->no_po,
                    'keterangan' => 'Auto dari PO: ' . $po->no_po,
                    'created_by' => $userId,
                ]);

                if ($sns && optional($d->barang)->has_sn) {
                    foreach ($sns as $sn) {
                        GudangSerialNumber::create([
                            'barang_id'     => $d->barang_id,
                            'masuk_id'      => $masuk->id,
                            'serial_number' => $sn,
                            'kondisi'       => 'baru',
                            'status'        => 'tersedia',
                        ]);
                    }
                }
                $dicatat++;
            }

            if ($dicatat === 0) {
                throw ValidationException::withMessages(['jumlah' => 'Isi jumlah barang yang datang minimal satu baris.']);
            }

            $semuaDatang = PoDetail::where('po_id', $po->id)->get()
                ->every(fn($x) => (int) $x->jumlah_diterima >= (int) $x->jumlah);
            $po->update(['status' => $semuaDatang ? 'selesai' : 'partial']);

            return $dicatat;
        });
    }

    /** @return string[] */
    private static function parseSerial(?string $teks): array
    {
        if ($teks === null || trim($teks) === '') {
            return [];
        }
        return array_values(array_filter(array_map('trim', preg_split('/\R/', $teks))));
    }
}
