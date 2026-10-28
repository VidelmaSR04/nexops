<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    private const OPD_PACKAGES = [
        '50 Mbps',
        '100 Mbps',
        '200 Mbps',
        '300 Mbps',
        '500 Mbps',
        '750 Mbps',
        '1 Gbps',
    ];

    private const OPD_LOCATION_DEFAULTS = [
        'Dinas Komunikasi dan Informatika' => [
            'area' => 'Kec. Situbondo',
            'address' => '72R2+WCX, Jl. Pb. Sudirman, Plaosan, Patokan, Kec. Situbondo, Kabupaten Situbondo, Jawa Timur 68312',
            'latitude' => -7.7076401,
            'longitude' => 114.0011062,
        ],
    ];

    public function index(Request $request)
    {
        $customers = Customer::where('type', 'opd')
            ->orderByDesc('registration_date')
            ->orderByDesc('id')
            ->get();
        $customersList = $customers->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'reporter_name' => $c->reporter_name ?? '-',
                'reporter_contact' => $c->reporter_contact ?? '-',
                'custNo' => $c->customer_code,
                'registration_date' => $c->registration_date?->format('Y-m-d'),
                'area' => $c->area ?? 'Situbondo',
                'status' => $c->status,
                'paket' => $c->package_name ?? '-',
                'package_name' => $c->package_name,
                'email' => $c->email ?? '-',
                'address' => $c->address ?? '-',
                'latitude' => $c->latitude,
                'longitude' => $c->longitude,
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
            'opdOptions' => $customers
                ->groupBy('name')
                ->map(function ($opdRecords): array {
                    $customer = $opdRecords->sortByDesc('id')->first();
                    $opdId = $opdRecords->min('id');
                    $defaults = self::OPD_LOCATION_DEFAULTS[$customer->name] ?? [];
                    $existingValue = fn ($value) => filled($value) && $value !== '-' ? $value : null;

                    return [
                        'id' => $opdId,
                        'name' => $customer->name,
                        'area' => $existingValue($customer->area) ?? ($defaults['area'] ?? null),
                        'address' => $existingValue($customer->address) ?? ($defaults['address'] ?? null),
                        'latitude' => $existingValue($customer->latitude) ?? ($defaults['latitude'] ?? null),
                        'longitude' => $existingValue($customer->longitude) ?? ($defaults['longitude'] ?? null),
                    ];
                })
                ->values(),
            'customerCodes' => Customer::withTrashed()->pluck('customer_code')->all(),
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
        if (! $request->expectsJson()) {
            $validated = $this->validateOpdDetails($request);
            $opdRecords = Customer::query()
                ->where('type', 'opd')
                ->where('name', $validated['name'])
                ->orderBy('id')
                ->get();
            $customer = $opdRecords->first(
                fn (Customer $record): bool => ! $this->isGeneratedOpdCode($record->customer_code),
            );

            if ($customer) {
                $customer->customer_code = $this->generateOpdCustomerCode();
                unset($validated['name']);
                $customer->fill($validated)->save();
            } else {
                $customer = Customer::create([
                    ...$validated,
                    'type' => 'opd',
                    'customer_code' => $this->generateOpdCustomerCode(),
                ]);
            }

            return redirect()->route('customers.index')->with('success', 'Data OPD berhasil disimpan.');
        }

        $validated = $this->validateCustomer($request);
        $validated['registration_date'] ??= $validated['type'] === 'opd' ? now()->toDateString() : null;
        $validated['customer_code'] = $validated['type'] === 'opd'
            ? $this->generateOpdCustomerCode()
            : 'CST'.str_pad((Customer::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
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

        if ($customer->type === 'opd') {
            $validated = $this->validateOpdDetails($request, false, $customer);
            unset($validated['reporter_name'], $validated['reporter_contact']);
        } else {
            $validated = $this->validateCustomer($request, $customer->id);
        }

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
            'reporter_contact' => ['nullable', 'string', 'regex:/^[0-9]{1,14}$/'],
            'type' => ['required', Rule::in($request->expectsJson()
                ? ['individual', 'business', 'opd']
                : ['opd'])],
            'email' => 'nullable|email|max:255',
            'area' => 'nullable|string|max:60',
            'address' => 'nullable|string',
            'package_name' => 'nullable|string|max:60',
            'status' => 'nullable|string|max:20',
            'registration_date' => ['nullable', 'date'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);
    }

    private function validateOpdDetails(
        Request $request,
        bool $includeName = true,
        ?Customer $existingCustomer = null,
    ): array
    {
        $rules = [
            'area' => ['required', 'string', 'max:60'],
            'address' => ['required', 'string', 'max:65535'],
            'package_name' => [
                'required',
                'string',
                'max:60',
                Rule::in(array_unique([
                    ...self::OPD_PACKAGES,
                    ...($existingCustomer && ! in_array($existingCustomer->package_name, self::OPD_PACKAGES, true)
                        ? [$existingCustomer->package_name]
                        : []),
                ])),
            ],
            'status' => ['required', Rule::in(['Aktif', 'Baru', 'Nonaktif'])],
            'registration_date' => ['required', 'date'],
            'latitude' => ['required', 'numeric', 'between:-8.05,-7.3'],
            'longitude' => ['required', 'numeric', 'between:113.5,114.65'],
        ];

        if ($includeName) {
            $rules['reporter_name'] = ['required', 'string', 'max:150'];
            $rules['reporter_contact'] = ['required', 'string', 'regex:/^[0-9]{1,14}$/'];
            $rules['name'] = [
                'required',
                'string',
                Rule::exists('customers', 'name')->where('type', 'opd'),
            ];
        } else {
            $rules['reporter_name'] = ['sometimes', 'nullable', 'string', 'max:150'];
            $rules['reporter_contact'] = ['sometimes', 'nullable', 'string', 'regex:/^[0-9]{1,14}$/'];
        }

        $validated = $request->validate($rules);

        return $validated;
    }

    private function generateOpdCustomerCode(): string
    {
        $sequence = Customer::withTrashed()
            ->where('customer_code', 'like', 'OPD%')
            ->pluck('customer_code')
            ->map(static fn (string $code): int => preg_match('/^OPD(\d+)$/', $code, $matches)
                ? (int) $matches[1]
                : 0)
            ->max() ?? 0;

        return 'OPD'.str_pad((string) ($sequence + 1), 4, '0', STR_PAD_LEFT);
    }

    private function isGeneratedOpdCode(?string $code): bool
    {
        return is_string($code) &&
            preg_match('/^OPD(?:\d+|-)/', $code) === 1;
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
