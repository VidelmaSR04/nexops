<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()
            ->withCount([
                'tickets',
                'tickets as active_tickets_count' => fn (Builder $tickets) => $tickets
                    ->whereNotIn('status', ['Resolved', 'Closed']),
            ]);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function (Builder $customers) use ($search) {
                $customers->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%')
                    ->orWhere('customer_code', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('area')) {
            $query->where('area', $request->string('area')->toString());
        }

        $customers = $query->orderByDesc('id')->get();
        $customersList = $customers->map(fn (Customer $customer) => $this->serializeCustomer($customer))->all();

        $stats = [
            'total' => Customer::count(),
            'active' => Customer::where('status', 'Aktif')->count(),
            'new' => Customer::where('status', 'Baru')->count(),
            'inactive' => Customer::where('status', 'Nonaktif')->count(),
        ];

        if ($this->expectsJson($request)) {
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

    public function show(Customer $customer): JsonResponse
    {
        $customer->load([
            'tickets' => fn (Builder $tickets) => $tickets
                ->select(['id', 'customer_id', 'ticket_number', 'title', 'status', 'created_at'])
                ->orderByDesc('created_at'),
            'assets' => fn (Builder $assets) => $assets
                ->select(['id', 'customer_id', 'asset_code', 'name', 'status']),
        ])->loadCount([
            'tickets',
            'tickets as active_tickets_count' => fn (Builder $tickets) => $tickets
                ->whereNotIn('status', ['Resolved', 'Closed']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail pelanggan berhasil diambil.',
            'data' => [
                'customer' => $this->serializeCustomer($customer),
                'ticket_history' => $customer->tickets,
                'assets' => $customer->assets,
            ],
        ]);
    }

    public function store(CustomerRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $customer = DB::transaction(function () use ($validated) {
            $customer = Customer::create([
                ...$validated,
                'customer_code' => $validated['customer_code'] ?? $this->nextCustomerCode(),
                'type' => $validated['type'] ?? 'individual',
            ]);

            return $customer->loadCount([
                'tickets',
                'tickets as active_tickets_count' => fn (Builder $tickets) => $tickets
                    ->whereNotIn('status', ['Resolved', 'Closed']),
            ]);
        });

        if ($this->expectsJson($request)) {
            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan.',
                'data' => $this->serializeCustomer($customer),
            ], 201);
        }

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan ke database.');
    }

    public function update(CustomerRequest $request, Customer $customer): JsonResponse|RedirectResponse
    {
        $customer->update($request->validated());
        $this->loadTicketCounts($customer->refresh());

        if ($this->expectsJson($request)) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diperbarui.',
                'data' => $this->serializeCustomer($customer),
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Data customer berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Customer $customer): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['Aktif', 'Baru', 'Nonaktif'])],
        ]);
        $customer->update(['status' => $validated['status']]);

        if ($this->expectsJson($request)) {
            return response()->json([
                'success' => true,
                'message' => 'Status pelanggan berhasil diperbarui.',
                'data' => $this->serializeCustomer($this->loadTicketCounts($customer->fresh())),
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Status customer berhasil diperbarui.');
    }

    public function destroy(Request $request, Customer $customer): JsonResponse|RedirectResponse
    {
        $customer->update(['status' => 'Nonaktif']);

        if ($this->expectsJson($request)) {
            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil dinonaktifkan.',
                'data' => $this->serializeCustomer($this->loadTicketCounts($customer->fresh())),
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer berhasil dinonaktifkan.');
    }

    private function serializeCustomer(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'nik' => $customer->nik ?? '',
            'custNo' => $customer->customer_code,
            'area' => $customer->area ?? '',
            'status' => $customer->status,
            'paket' => $customer->package_name ?? '',
            'phone' => $customer->phone ?? '',
            'email' => $customer->email ?? '',
            'address' => $customer->address ?? '',
            'tagihan' => $customer->monthly_bill ?? '',
            'tickets_count' => $customer->tickets_count ?? 0,
            'active_tickets_count' => $customer->active_tickets_count ?? 0,
        ];
    }

    private function nextCustomerCode(): string
    {
        $lastSequence = Customer::withTrashed()
            ->lockForUpdate()
            ->pluck('customer_code')
            ->reduce(function (int $highest, string $customerCode): int {
                if (preg_match('/^CST(\d+)$/', $customerCode, $matches)) {
                    return max($highest, (int) $matches[1]);
                }

                return $highest;
            }, 0);

        return 'CST' . str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
    }

    private function loadTicketCounts(Customer $customer): Customer
    {
        return $customer->loadCount([
            'tickets',
            'tickets as active_tickets_count' => fn (Builder $tickets) => $tickets
                ->whereNotIn('status', ['Resolved', 'Closed']),
        ]);
    }

    private function expectsJson(Request $request): bool
    {
        return $request->is('api/*') || $request->wantsJson();
    }
}
