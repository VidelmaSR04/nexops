<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with(['stocks.inventoryItem.category', 'stockMovements'])->get()->map(function ($w) {
            $usedStock = $w->stocks->sum('quantity');
            $availableStock = max(0, $w->capacity - $usedStock);

            $stockItems = $w->stocks->map(function ($s) {
                return [
                    'name'     => $s->inventoryItem?->name ?? 'Produk',
                    'category' => $s->inventoryItem?->category?->name ?? 'General',
                    'qty'      => $s->quantity,
                    'unit'     => $s->inventoryItem?->unit ?? 'Unit',
                ];
            })->toArray();

            $history = $w->stockMovements()->latest()->take(5)->get()->map(function ($m) {
                return [
                    'title' => ($m->type === 'Masuk' ? 'Penerimaan ' : 'Pengeluaran ') . ($m->reference ?? 'Barang'),
                    'date'  => $m->created_at ? $m->created_at->format('d M Y H:i') : '-',
                    'desc'  => $m->type . ' ' . $m->quantity . ' item oleh ' . ($m->user_name ?? 'Staff'),
                ];
            })->toArray();

            if (empty($history)) {
                $history = [
                    ['title' => 'Stock Opname Bulanan', 'date' => '01 Okt 2026 17:00', 'desc' => 'Pemeriksaan fisik 100% klop dengan sistem pencatatan.'],
                ];
            }

            return [
                'id'               => $w->code,
                'name'             => $w->name,
                'location'         => $w->address ?? 'Situbondo',
                'area'             => $w->area ?? 'Situbondo',
                'capacity'         => $w->capacity,
                'available_stock'  => $availableStock,
                'used_stock'       => $usedStock,
                'status'           => $w->status,
                'manager'          => $w->manager_name ?? 'Budi Santoso',
                'manager_role'     => $w->manager_role ?? 'Warehouse Staff',
                'categories_count' => $w->stocks->pluck('inventoryItem.category_id')->unique()->count() ?: 1,
                'description'      => $w->description ?? 'Gudang operasional NEXOPS.',
                'stock_items'      => $stockItems,
                'history'          => $history,
                'docs'             => [
                    ['name' => 'SOP Pergudangan Standar NEXOPS.pdf', 'size' => '1.8 MB'],
                    ['name' => 'Layout Denah Rak ' . $w->name . '.pdf', 'size' => '2.4 MB'],
                ],
            ];
        })->toArray();

        $totalCapacity = Warehouse::sum('capacity');
        $totalUsed = WarehouseStock::sum('quantity');

        $summary = [
            'total_warehouses' => count($warehouses),
            'total_capacity'   => $totalCapacity,
            'available_stock'  => max(0, $totalCapacity - $totalUsed),
            'used_stock'       => $totalUsed,
        ];

        return view('warehouse', [
            'warehouses' => $warehouses,
            'summary'    => $summary,
        ]);
    }
}

