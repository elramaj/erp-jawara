<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private function cekAkses()
    {
        if (!in_array(auth()->user()->role_id, [1, 2, 11])) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $this->cekAkses();
        $suppliers = Supplier::orderBy('nama')->get();
        return view('keuangan.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        $this->cekAkses();
        return view('keuangan.supplier.create');
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'kode' => 'required|unique:suppliers,kode',
            'nama' => 'required|string|max:150',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        Supplier::create($request->only(['kode', 'nama', 'alamat', 'telepon', 'email', 'pic']) + ['is_active' => 1]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function edit(Supplier $supplier)
    {
        $this->cekAkses();
        return view('keuangan.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $this->cekAkses();
        $request->validate([
            'kode' => 'required|unique:suppliers,kode,' . $supplier->id,
            'nama' => 'required|string|max:150',
        ]);

        $supplier->update($request->only(['kode', 'nama', 'alamat', 'telepon', 'email', 'pic']) + [
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diupdate!');
    }

    public function destroy(Supplier $supplier)
    {
        $this->cekAkses();
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus!');
    }
}