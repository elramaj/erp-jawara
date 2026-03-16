<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private function cekAkses()
{
    if (!in_array(auth()->user()->role_id, [1, 2, 3, 11, 14])) {
        abort(403, 'Akses ditolak.');
    }
}

    public function index()
    {
        $this->cekAkses();
        $customers = Customer::orderBy('nama')->get();
        return view('keuangan.customer.index', compact('customers'));
    }

    public function create()
    {
        $this->cekAkses();
        return view('keuangan.customer.create');
    }

    public function store(Request $request)
    {
        $this->cekAkses();
        $request->validate([
            'kode' => 'required|unique:customers,kode',
            'nama' => 'required|string|max:150',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        Customer::create($request->only(['kode', 'nama', 'alamat', 'telepon', 'email', 'pic']) + ['is_active' => 1]);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan!');
    }

    public function edit(Customer $customer)
    {
        $this->cekAkses();
        return view('keuangan.customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->cekAkses();
        $request->validate([
            'kode' => 'required|unique:customers,kode,' . $customer->id,
            'nama' => 'required|string|max:150',
        ]);

        $customer->update($request->only(['kode', 'nama', 'alamat', 'telepon', 'email', 'pic']) + [
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil diupdate!');
    }

    public function destroy(Customer $customer)
    {
        $this->cekAkses();
        $customer->delete();
        return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus!');
    }
}