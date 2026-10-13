<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $products = InventoryItem::with(['category', 'stocks.warehouse', 'movements'])->get()->map(function ($item) {
            $totalStock = $item->stocks->sum('quantity');
            $primaryStock = $item->stocks->sortByDesc('quantity')->first();
            $primaryWh = $primaryStock?->warehouse;

            $status = $item->status;
            if ($totalStock == 0) {
                $status = 'Habis';
            } elseif ($totalStock <= $item->min_stock) {
                $status = 'Low Stock';
            }

            $whStocks = $item->stocks->map(function ($s) {
                return [
                    'name'  => $s->warehouse?->name ?? 'Gudang Utama',
                    'stock' => $s->quantity,
                    'min'   => $s->min_quantity,
                ];
            })->toArray();

            $movements = $item->movements()->latest()->take(5)->get()->map(function ($m) {
                return [
                    'date' => $m->created_at ? $m->created_at->format('d M Y H:i') : '-',
                    'type' => $m->type,
                    'qty'  => $m->quantity,
                    'ref'  => $m->reference ?? '-',
                    'user' => $m->user_name ?? 'Staff',
                ];
            })->toArray();

            return [
                'id'             => $item->sku,
                'name'           => $item->name,
                'category'       => $item->category?->name ?? 'Accessories',
                'unit'           => $item->unit,
                'total_stock'    => $totalStock,
                'min_stock'      => $item->min_stock,
                'status'         => $status,
                'location'       => $primaryWh?->name ?? 'Gudang Utama',
                'address'        => $primaryWh?->address ?? 'Jl. Raya Situbondo No. 12',
                'serial_example' => $item->serial_example ?? ($item->sku . '-SN'),
                'description'    => $item->description ?? 'Deskripsi item inventaris NEXOPS.',
                'warehouses'     => $whStocks,
                'movements'      => $movements,
                'related'        => [
                    'active_assets' => 12,
                    'open_tickets'  => 1,
                ],
            ];
        })->toArray();

        $summary = [
            'total_products' => InventoryItem::count(),
            'total_stock'    => WarehouseStock::sum('quantity'),
            'low_stock'      => InventoryItem::where('status', 'Low Stock')->count(),
            'warehouse'      => Warehouse::count(),
        ];

        $categories = Category::pluck('name')->toArray();

        return view('inventory', [
            'products'   => $products,
            'summary'    => $summary,
            'categories' => $categories,
        ]);
    }
}

