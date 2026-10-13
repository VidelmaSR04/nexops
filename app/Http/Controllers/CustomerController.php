<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customersList = Customer::all()->map(function ($c) {
            return [
                'id'      => $c->id,
                'name'    => $c->name,
                'nik'     => $c->nik ?? '-',
                'custNo'  => $c->customer_code,
                'area'    => $c->area ?? 'Situbondo',
                'status'  => $c->status,
                'paket'   => $c->package_name ?? '50 Mbps',
                'phone'   => $c->phone ?? '-',
                'email'   => $c->email ?? '-',
                'address' => $c->address ?? '-',
                'tagihan' => $c->monthly_bill ?? 'Rp0',
            ];
        })->toArray();

        $stats = [
            'total'    => Customer::count(),
            'active'   => Customer::where('status', 'Aktif')->count(),
            'new'      => Customer::where('status', 'Baru')->count(),
            'inactive' => Customer::where('status', 'Nonaktif')->count(),
        ];

        return view('customers', [
            'customersData' => $customersList,
            'stats'         => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'nik'          => 'nullable|string|max:30',
            'phone'        => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:255',
            'area'         => 'nullable|string|max:60',
            'address'      => 'nullable|string',
            'package_name' => 'nullable|string|max:60',
            'status'       => 'nullable|string|max:20',
        ]);

        $lastId = Customer::max('id') ?? 0;
        $validated['customer_code'] = 'CST' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = $validated['status'] ?? 'Aktif';

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan ke database.');
    }
}

