<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('type', 'opd')->orderBy('name')->get();
        $customersList = $customers->map(function ($c) {
            return [
                'id'      => $c->id,
                'name'    => $c->name,
                'reporter_name' => $c->reporter_name ?? '-',
                'reporter_contact' => $c->reporter_contact ?? '-',
                'nik'     => $c->nik ?? '-',
                'custNo'  => $c->customer_code,
                'area'    => $c->area ?? 'Situbondo',
                'status'  => $c->status,
                'paket'   => $c->package_name ?? '50 Mbps',
                'package_name' => $c->package_name,
                'phone'   => $c->phone ?? '-',
                'email'   => $c->email ?? '-',
                'address' => $c->address ?? '-',
                'tagihan' => $c->monthly_bill ?? 'Rp0',
            ];
        })->toArray();

        $stats = [
            'total'    => $customers->count(),
            'active'   => $customers->where('status', 'Aktif')->count(),
            'new'      => $customers->where('status', 'Baru')->count(),
            'inactive' => $customers->where('status', 'Nonaktif')->count(),
        ];

        return view('customers', [
            'customersData' => $customersList,
            'stats'         => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCustomer($request);

        $lastId = Customer::max('id') ?? 0;
        $validated['customer_code'] = 'CST' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = $validated['status'] ?? 'Aktif';

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'OPD berhasil ditambahkan ke database.');
    }

    public function update(Request $request, $customerId)
    {
        $customer = Customer::where('type', 'opd')->findOrFail($customerId);
        $validated = $this->validateCustomer($request, $customer->id);
        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Data OPD berhasil diperbarui.');
    }

    private function validateCustomer(Request $request, ?int $customerId = null): array
    {
        $nameRule = Rule::unique('customers', 'name');
        if ($customerId !== null) {
            $nameRule->ignore($customerId);
        }

        return $request->validate([
            'name'         => ['required', 'string', 'max:255', $nameRule],
            'reporter_name' => 'nullable|string|max:150',
            'reporter_contact' => 'nullable|string|max:50',
            'type'         => 'required|in:opd',
            'nik'          => 'nullable|string|max:30',
            'phone'        => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:255',
            'area'         => 'nullable|string|max:60',
            'address'      => 'nullable|string',
            'package_name' => 'nullable|string|max:60',
            'status'       => 'nullable|string|max:20',
        ]);
    }
}
