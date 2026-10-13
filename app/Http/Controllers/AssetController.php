<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with(['category', 'warehouse', 'tickets'])->get()->map(function ($a) {
            $location = $a->location_name ?? ($a->warehouse?->name ?? 'Gudang Utama');

            return [
                'id'              => $a->asset_code,
                'name'            => $a->name,
                'category'        => $a->category?->name ?? 'Perangkat Jaringan',
                'serial'          => $a->serial_number ?? '-',
                'location'        => $location,
                'status'          => $a->status,
                'vendor'          => $a->vendor ?? '-',
                'purchase_date'   => $a->purchase_date ? $a->purchase_date->format('d F Y') : '-',
                'warranty'        => $a->warranty ?? '12 Bulan',
                'description'     => $a->description ?? 'Deskripsi aset operasional NEXOPS.',
                'specs'           => $a->specs ?? 'Spesifikasi standar operasional',
                'icon'            => $a->icon ?? 'router',
                'history'         => [
                    ['title' => 'Asset didaftarkan', 'date' => ($a->purchase_date ? $a->purchase_date->format('d M Y') : '12 Jan 2024') . ' 10:00', 'desc' => 'Penerimaan barang dari vendor ' . ($a->vendor ?? 'resmi') . '.'],
                    ['title' => 'Status asset diperbarui', 'date' => '08 Okt 2026 09:15', 'desc' => 'Pemeriksaan berkala fungsi operasional selesai.'],
                ],
                'related_tickets' => $a->tickets->count(),
                'related_devices' => 1,
                'docs'            => [
                    ['name' => 'Manual Book ' . $a->name . '.pdf', 'size' => '2.4 MB'],
                    ['name' => 'Faktur & Kartu Garansi.pdf', 'size' => '1.1 MB'],
                ],
            ];
        })->toArray();

        $summary = [
            'total_asset' => Asset::count(),
            'active'      => Asset::where('status', 'Aktif')->count(),
            'maintenance' => Asset::where('status', 'Maintenance')->count(),
            'broken'      => Asset::where('status', 'Rusak')->count(),
        ];

        return view('assets', [
            'assets'  => $assets,
            'summary' => $summary,
        ]);
    }
}

