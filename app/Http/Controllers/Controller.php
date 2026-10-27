<?php

namespace App\Http\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Controller
{
    /**
     * company_id yang dipakai untuk MENYIMPAN data baru.
     *
     * Super Admin tidak terikat ke satu PT (company_id NULL). Kalau ia
     * menyimpan data baru, baris itu jadi "yatim" -- tidak terlihat oleh
     * PT mana pun. Jadi untuk Super Admin tanpa PT, penyimpanan dihentikan
     * dengan pesan yang jelas (bukan error 500 / data hilang diam-diam).
     */
    protected function companyIdUntukSimpan(): int
    {
        $id = auth()->user()?->company_id;

        if (!$id) {
            throw new HttpResponseException(
                back()->withInput()->with('error',
                    'Akun Super Admin tidak terikat ke PT mana pun, jadi data baru tidak bisa disimpan di sini. '
                    . 'Login sebagai Admin PT yang bersangkutan untuk mencatat data ini.')
            );
        }

        return (int) $id;
    }
}
