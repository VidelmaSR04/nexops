<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::where('type', 'opd')->orderBy('name')->get();
        $customersList = $customers->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'reporter_name' => $c->reporter_name ?? '-',
                'reporter_contact' => $c->reporter_contact ?? '-',
                'custNo' => $c->customer_code,
                'area' => $c->area ?? 'Situbondo',
                'status' => $c->status,
                'paket' => $c->package_name ?? '50 Mbps',
                'package_name' => $c->package_name,
                'email' => $c->email ?? '-',
                'address' => $c->address ?? '-',
                'tagihan' => $c->monthly_bill ?? 'Rp0',
            ];
        })->toArray();

        $stats = [
            'total' => $customers->count(),
            'active' => $customers->where('status', 'Aktif')->count(),
            'new' => $customers->where('status', 'Baru')->count(),
            'inactive' => $customers->where('status', 'Nonaktif')->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diambil.',
                'data' => [
                    'customers' => $customersList,
                    'stats' => $stats,
                ],
            ]);
        }

        return view('customers', [
            'customersData' => $customersList,
            'stats' => $stats,
        ]);
    }

    public function show(Request $request, Customer $customer): JsonResponse
    {
        if (! $request->expectsJson()) {
            abort_unless($customer->type === 'opd', 404);
        }

        $customerData = $this->customerData($customer);
        $customerData['active_tickets_count'] = $customer->tickets()
            ->whereNotIn('status', ['Resolved', 'Closed'])
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => $customerData,
                'ticket_history' => $customer->tickets()
                    ->latest()
                    ->get(['ticket_number', 'title', 'status', 'created_at']),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validateCustomer($request);
        $validated['customer_code'] = 'CST'.str_pad((Customer::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = $validated['status'] ?? 'Aktif';

        $customer = Customer::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $this->customerData($customer->loadCount('tickets')),
            ], 201);
        }

        return redirect()->route('customers.index')->with('success', 'OPD berhasil ditambahkan ke database.');
    }

    public function update(Request $request, $customerId): JsonResponse|RedirectResponse
    {
        $customer = Customer::query();
        if (! $request->expectsJson()) {
            $customer->where('type', 'opd');
        }
        $customer = $customer->findOrFail($customerId);
        $validated = $this->validateCustomer($request, $customer->id);
        $customer->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $this->customerData($customer->fresh()->loadCount('tickets')),
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Data OPD berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Customer $customer): JsonResponse|RedirectResponse
    {
        if (! $request->expectsJson()) {
            abort_unless($customer->type === 'opd', 404);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Aktif', 'Baru', 'Nonaktif'])],
        ]);
        $customer->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $this->customerData($customer->fresh()->loadCount('tickets')),
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Status OPD berhasil diperbarui.');
    }

    public function destroy(Request $request, Customer $customer): JsonResponse|RedirectResponse
    {
        if (! $request->expectsJson()) {
            abort_unless($customer->type === 'opd', 404);
        }
        $customer->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('customers.index')->with('success', 'OPD berhasil dinonaktifkan.');
    }

    private function validateCustomer(Request $request, ?int $customerId = null): array
    {
        if ($request->exists('phone') && ! $request->filled('reporter_contact')) {
            $request->merge(['reporter_contact' => $request->input('phone')]);
        }

        $nameRule = Rule::unique('customers', 'name');
        if ($customerId !== null) {
            $nameRule->ignore($customerId);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255', $nameRule],
            'reporter_name' => 'nullable|string|max:150',
            'reporter_contact' => 'nullable|string|max:50',
            'type' => ['required', Rule::in($request->expectsJson()
                ? ['individual', 'business', 'opd']
                : ['opd'])],
            'email' => 'nullable|email|max:255',
            'area' => 'nullable|string|max:60',
            'address' => 'nullable|string',
            'package_name' => 'nullable|string|max:60',
            'status' => 'nullable|string|max:20',
        ]);
    }

    private function customerData(Customer $customer): array
    {
        $data = $customer->toArray();
        $data['custNo'] = $customer->customer_code;

        if (array_key_exists('tickets_count', $data)) {
            $data['tickets_count'] = (int) $data['tickets_count'];
        }

        return $data;
    }
}
