<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profil.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:150',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        return redirect()->route('profil.index')
            ->with('success', 'Profil berhasil diupdate!');
    }

    public function gantiPassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'password_lama'     => 'required',
            'password_baru'     => 'required|min:6|confirmed',
        ], [
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.min'       => 'Password minimal 6 karakter.',
        ]);

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai!']);
        }

        $user->update([
            'password' => bcrypt($request->password_baru),
        ]);

        return redirect()->route('profil.index')
            ->with('success', 'Password berhasil diganti!');
    }
}