<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\LoginHistory;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketAttachment;
use App\Models\TicketMaterial;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NexopsDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. KATEGORI
        $categoryNames = [
            'Modem/ONT',
            'Accessories',
            'Perangkat FO',
            'Kabel',
            'Perangkat Jaringan',
            'Server',
            'Laptop',
            'Peralatan',
            'Keamanan',
        ];

        $categories = [];
        foreach ($categoryNames as $name) {
            $categories[$name] = Category::firstOrCreate(['name' => $name]);
        }

        // 2. USER TAMBAHAN (jika belum ada)
        $adminUser = User::first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name'     => 'Admin NexOps',
                'email'    => 'admin@nexops.id',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]);
        }

        $budiUser = User::firstOrCreate(
            ['email' => 'budi.santoso@nexops.id'],
            [
                'name'     => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role'     => 'technician',
            ]
        );

        $ahmadUser = User::firstOrCreate(
            ['email' => 'ahmad.fauzi@nexops.id'],
            [
                'name'     => 'Ahmad Fauzi',
                'password' => Hash::make('password'),
                'role'     => 'technician',
            ]
        );

        // 3. GUDANG (WAREHOUSES)
        $warehousesData = [
            [
                'code'         => 'WH-001',
                'name'         => 'Gudang Utama',
                'address'      => 'Jl. Raya Situbondo No. 12',
                'area'         => 'Situbondo',
                'capacity'     => 50000,
                'status'       => 'Aktif',
                'manager_name' => 'Budi Santoso',
                'manager_role' => 'Warehouse Staff',
                'description'  => 'Gudang penyimpanan utama untuk perangkat pasif/aktif dan material operasional lapangan.',
            ],
            [
                'code'         => 'WH-002',
                'name'         => 'Gudang Situbondo',
                'address'      => 'Jl. PB Sudirman No. 45',
                'area'         => 'Situbondo',
                'capacity'     => 30000,
                'status'       => 'Aktif',
                'manager_name' => 'Ahmad Fauzi',
                'manager_role' => 'Hub Logistics Officer',
                'description'  => 'Gudang hub regional area perkotaan Situbondo untuk percepatan perbaikan customer residensial.',
            ],
            [
                'code'         => 'WH-003',
                'name'         => 'Gudang Besuki',
                'address'      => 'Jl. Raya Besuki No. 78',
                'area'         => 'Besuki',
                'capacity'     => 20000,
                'status'       => 'Maintenance',
                'manager_name' => 'Dimas Prayoga',
                'manager_role' => 'Logistics Staff',
                'description'  => 'Gudang transit wilayah barat. Sedang proses penataan ulang rak penyimpanan dan perbaikan sistem sirkulasi udara.',
            ],
            [
                'code'         => 'WH-004',
                'name'         => 'Gudang Lapangan',
                'address'      => 'Lokasi Site A',
                'area'         => 'Area Lapangan',
                'capacity'     => 10000,
                'status'       => 'Aktif',
                'manager_name' => 'Rizky Ananda',
                'manager_role' => 'Field Material Coordinator',
                'description'  => 'Depo kontainer logistik cepat tanggap di lokasi project deployment jaringan FO baru.',
            ],
        ];

        $warehouses = [];
        foreach ($warehousesData as $whData) {
            $warehouses[$whData['code']] = Warehouse::updateOrCreate(
                ['code' => $whData['code']],
                $whData
            );
        }

        // 4. INVENTORY ITEMS (PRODUK BARANG)
        $inventoryData = [
            [
                'sku'            => 'PRD-001',
                'name'           => 'ONT ZTE F670L',
                'category_name'  => 'Modem/ONT',
                'unit'           => 'Unit',
                'min_stock'      => 10,
                'status'         => 'Aktif',
                'serial_example' => 'ZTE001234',
                'description'    => 'Optical Network Terminal (ONT) dual-band GPON dengan 4 port Gigabit Ethernet dan WiFi 2.4/5GHz.',
                'stocks'         => ['WH-001' => [70, 10], 'WH-002' => [30, 10], 'WH-003' => [20, 5]],
            ],
            [
                'sku'            => 'PRD-002',
                'name'           => 'ONT Huawei HG8245H',
                'category_name'  => 'Modem/ONT',
                'unit'           => 'Unit',
                'min_stock'      => 10,
                'status'         => 'Aktif',
                'serial_example' => 'HWT778120',
                'description'    => 'EchoLife HG8245H GPON ONT dengan 4 port GE, 2 port POTS, dan 1 port USB.',
                'stocks'         => ['WH-002' => [55, 10], 'WH-001' => [30, 10]],
            ],
            [
                'sku'            => 'PRD-003',
                'name'           => 'RJ45 Connector',
                'category_name'  => 'Accessories',
                'unit'           => 'Pack',
                'min_stock'      => 100,
                'status'         => 'Aktif',
                'serial_example' => 'CAT6-CON-420',
                'description'    => 'Konektor RJ45 Cat6 modular plug berlapis emas 50 micron untuk kabel UTP/STP.',
                'stocks'         => ['WH-001' => [300, 70], 'WH-003' => [120, 30]],
            ],
            [
                'sku'            => 'PRD-004',
                'name'           => 'Fiber Patchcord',
                'category_name'  => 'Accessories',
                'unit'           => 'Pcs',
                'min_stock'      => 20,
                'status'         => 'Low Stock',
                'serial_example' => 'PC-SCUPC-3M',
                'description'    => 'Patchcord Single Mode SC/UPC to SC/UPC simplex panjang 3 meter diameter 3.0mm.',
                'stocks'         => ['WH-003' => [15, 10], 'WH-001' => [50, 10]],
            ],
            [
                'sku'            => 'PRD-005',
                'name'           => 'Adapter 12V',
                'category_name'  => 'Accessories',
                'unit'           => 'Pcs',
                'min_stock'      => 25,
                'status'         => 'Low Stock',
                'serial_example' => 'ADP-12V1.5A',
                'description'    => 'Power Adaptor Switching AC 100-240V ke DC 12V 1.5A jack standard 5.5mm.',
                'stocks'         => ['WH-001' => [48, 25]],
            ],
            [
                'sku'            => 'PRD-006',
                'name'           => 'Splitter 1:8',
                'category_name'  => 'Perangkat FO',
                'unit'           => 'Pcs',
                'min_stock'      => 50,
                'status'         => 'Aktif',
                'serial_example' => 'PLC-1x8-SCUPC',
                'description'    => 'PLC Optical Splitter 1:8 SC/UPC box type untuk distribusi Fiber to the Home (FTTH).',
                'stocks'         => ['WH-001' => [140, 30], 'WH-002' => [60, 20]],
            ],
            [
                'sku'            => 'PRD-007',
                'name'           => 'ODP Box',
                'category_name'  => 'Perangkat FO',
                'unit'           => 'Unit',
                'min_stock'      => 20,
                'status'         => 'Aktif',
                'serial_example' => 'ODP-8C-OUTDOOR',
                'description'    => 'Optical Distribution Point 8/16 Core IP65 Waterproof dengan kunci proteksi luar ruangan.',
                'stocks'         => ['WH-004' => [45, 10], 'WH-001' => [30, 10]],
            ],
            [
                'sku'            => 'PRD-008',
                'name'           => 'Kabel Dropcore',
                'category_name'  => 'Kabel',
                'unit'           => 'Roll',
                'min_stock'      => 30,
                'status'         => 'Aktif',
                'serial_example' => 'DC-1C-1000M',
                'description'    => 'Kabel Drop Fiber Optik 1 Core 3 Seling Kawat Baja panjang 1000m/roll.',
                'stocks'         => ['WH-001' => [90, 20], 'WH-004' => [60, 10]],
            ],
            [
                'sku'            => 'PRD-009',
                'name'           => 'Patch Panel',
                'category_name'  => 'Perangkat Jaringan',
                'unit'           => 'Unit',
                'min_stock'      => 10,
                'status'         => 'Aktif',
                'serial_example' => 'PP-24P-CAT6',
                'description'    => 'Patch Panel 24 Port 1U 19-inch Rackmount Cat6 UTP with rear cable manager.',
                'stocks'         => ['WH-001' => [35, 10]],
            ],
            [
                'sku'            => 'PRD-010',
                'name'           => 'Router Mikrotik',
                'category_name'  => 'Perangkat Jaringan',
                'unit'           => 'Unit',
                'min_stock'      => 5,
                'status'         => 'Low Stock',
                'serial_example' => 'RB750Gr3-HEX',
                'description'    => 'RouterBOARD hEX 5x Gigabit Ethernet, Dual Core 880MHz CPU, 256MB RAM, RouterOS L4.',
                'stocks'         => ['WH-001' => [15, 5], 'WH-002' => [5, 2]],
            ],
        ];

        $items = [];
        foreach ($inventoryData as $data) {
            $cat = $categories[$data['category_name']] ?? null;
            $item = InventoryItem::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'name'           => $data['name'],
                    'category_id'    => $cat ? $cat->id : null,
                    'unit'           => $data['unit'],
                    'min_stock'      => $data['min_stock'],
                    'status'         => $data['status'],
                    'serial_example' => $data['serial_example'],
                    'description'    => $data['description'],
                ]
            );
            $items[$data['sku']] = $item;

            // Masukkan stok per gudang
            foreach ($data['stocks'] as $whCode => [$qty, $minQty]) {
                if (isset($warehouses[$whCode])) {
                    WarehouseStock::updateOrCreate(
                        [
                            'warehouse_id'      => $warehouses[$whCode]->id,
                            'inventory_item_id' => $item->id,
                        ],
                        [
                            'quantity'     => $qty,
                            'min_quantity' => $minQty,
                        ]
                    );
                }
            }
        }

        // Customers are managed through the OPD seeder and application.
        $customers = [];

        // 6. TECHNICIANS (TEKNISI)
        $techniciansData = [
            ['name' => 'Budi Santoso',    'nik' => 'NIK001234', 'area' => 'Situbondo', 'status' => 'On Task',   'position' => 'Teknisi Senior', 'phone' => '0812 3456 7890', 'email' => 'budi.santoso@nexops.id',    'joined_at' => '2024-01-12', 'exp_years' => 3, 'rating' => 4.8],
            ['name' => 'Ahmad Fauzi',     'nik' => 'NIK001235', 'area' => 'Besuki',    'status' => 'On Task',   'position' => 'Teknisi',        'phone' => '0813 5521 7742', 'email' => 'ahmad.fauzi@nexops.id',     'joined_at' => '2024-03-04', 'exp_years' => 2, 'rating' => 4.6],
            ['name' => 'Dimas Priyanto',  'nik' => 'NIK001236', 'area' => 'Situbondo', 'status' => 'On Task',   'position' => 'Teknisi',        'phone' => '0852 1190 3388', 'email' => 'dimas.priyanto@nexops.id',  'joined_at' => '2024-02-19', 'exp_years' => 3, 'rating' => 4.7],
            ['name' => 'Rizky Maulana',   'nik' => 'NIK001237', 'area' => 'Besuki',    'status' => 'Available', 'position' => 'Teknisi',        'phone' => '0821 4410 9025', 'email' => 'rizky.maulana@nexops.id',   'joined_at' => '2024-06-10', 'exp_years' => 2, 'rating' => 4.5],
            ['name' => 'Fajar Ramadhan',  'nik' => 'NIK001238', 'area' => 'Lapangan',  'status' => 'Available', 'position' => 'Teknisi',        'phone' => '0857 3301 6648', 'email' => 'fajar.ramadhan@nexops.id',  'joined_at' => '2024-08-05', 'exp_years' => 2, 'rating' => 4.4],
            ['name' => 'Siti Nurhaliza',  'nik' => 'NIK001239', 'area' => 'Situbondo', 'status' => 'On Task',   'position' => 'Teknisi',        'phone' => '0813 9027 4413', 'email' => 'siti.nurhaliza@nexops.id',  'joined_at' => '2024-04-22', 'exp_years' => 2, 'rating' => 4.9],
            ['name' => 'Andi Saputra',    'nik' => 'NIK001240', 'area' => 'Besuki',    'status' => 'Offline',   'position' => 'Teknisi',        'phone' => '0878 6640 1259', 'email' => 'andi.saputra@nexops.id',    'joined_at' => '2025-01-13', 'exp_years' => 1, 'rating' => 4.2],
            ['name' => 'Muhammad Iqbal',  'nik' => 'NIK001241', 'area' => 'Lapangan',  'status' => 'On Task',   'position' => 'Teknisi',        'phone' => '0812 7788 3004', 'email' => 'muhammad.iqbal@nexops.id',  'joined_at' => '2024-09-16', 'exp_years' => 2, 'rating' => 4.6],
            ['name' => 'Dewi Lestari',    'nik' => 'NIK001242', 'area' => 'Situbondo', 'status' => 'Available', 'position' => 'Teknisi',        'phone' => '0859 2214 7761', 'email' => 'dewi.lestari@nexops.id',    'joined_at' => '2025-02-03', 'exp_years' => 1, 'rating' => 4.5],
            ['name' => 'Agus Setiawan',   'nik' => 'NIK001243', 'area' => 'Besuki',    'status' => 'Offline',   'position' => 'Teknisi',        'phone' => '0822 5503 8196', 'email' => 'agus.setiawan@nexops.id',   'joined_at' => '2024-05-27', 'exp_years' => 2, 'rating' => 4.1],
            ['name' => 'Rina Amelia',     'nik' => 'NIK001244', 'area' => 'Situbondo', 'status' => 'On Task',   'position' => 'Teknisi',        'phone' => '0838 4096 2257', 'email' => 'rina.amelia@nexops.id',     'joined_at' => '2025-05-12', 'exp_years' => 1, 'rating' => 4.3],
            ['name' => 'Wahyu Pratama',   'nik' => 'NIK001245', 'area' => 'Lapangan',  'status' => 'On Task',   'position' => 'Teknisi',        'phone' => '0851 7733 9120', 'email' => 'wahyu.pratama@nexops.id',   'joined_at' => '2025-03-24', 'exp_years' => 1, 'rating' => 4.4],
        ];

        $technicians = [];
        foreach ($techniciansData as $tData) {
            $tech = Technician::updateOrCreate(
                ['nik' => $tData['nik']],
                $tData
            );
            $technicians[$tData['name']] = $tech;
        }

        // 7. ASSETS
        $assetsData = [
            [
                'asset_code'    => 'AST-001',
                'name'          => 'ONT ZTE F670L',
                'category_name' => 'Modem/ONT',
                'serial_number' => 'ZTE001234',
                'location_name' => 'Gudang Utama',
                'wh_code'       => 'WH-001',
                'status'        => 'Aktif',
                'vendor'        => 'ZTE',
                'purchase_date' => '2024-01-12',
                'warranty'      => '12 Bulan',
                'specs'         => 'GPON, 4x Gigabit Ethernet, 2x POTS, Dual-Band Wi-Fi 2.4/5GHz',
                'icon'          => 'router',
                'description'   => 'ONT ZTE F670L untuk layanan internet dengan kecepatan tinggi dan stabil.',
            ],
            [
                'asset_code'    => 'AST-002',
                'name'          => 'ONT Huawei HG8245H',
                'category_name' => 'Modem/ONT',
                'serial_number' => 'HUA001567',
                'location_name' => 'Gudang Situbondo',
                'wh_code'       => 'WH-002',
                'status'        => 'Aktif',
                'vendor'        => 'Huawei',
                'purchase_date' => '2024-02-15',
                'warranty'      => '12 Bulan',
                'specs'         => '4 GE ports, 2 POTS ports, 1 USB port, Wi-Fi 802.11b/g/n',
                'icon'          => 'router',
                'description'   => 'EchoLife HG8245H GPON terminal pelanggan dengan keandalan transmisi tinggi.',
            ],
            [
                'asset_code'    => 'AST-003',
                'name'          => 'Router Mikrotik',
                'category_name' => 'Perangkat Jaringan',
                'serial_number' => 'MTK001890',
                'location_name' => 'Gudang Utama',
                'wh_code'       => 'WH-001',
                'status'        => 'Aktif',
                'vendor'        => 'Mikrotik',
                'purchase_date' => '2024-01-10',
                'warranty'      => '24 Bulan',
                'specs'         => 'Dual Core 880MHz, 256MB RAM, 5x Gigabit LAN, RouterOS Level 4',
                'icon'          => 'network',
                'description'   => 'RouterBOARD hEX RB750Gr3 untuk manajemen bandwidth dan routing core cabang.',
            ],
            [
                'asset_code'    => 'AST-004',
                'name'          => 'Switch 24 Port',
                'category_name' => 'Perangkat Jaringan',
                'serial_number' => 'SW001234',
                'location_name' => 'Gudang Besuki',
                'wh_code'       => 'WH-003',
                'status'        => 'Maintenance',
                'vendor'        => 'TP-Link',
                'purchase_date' => '2024-03-20',
                'warranty'      => '12 Bulan',
                'specs'         => '24x 10/100/1000Mbps RJ45 PoE+, 4x Gigabit SFP Slots',
                'icon'          => 'server',
                'description'   => 'Switch Gigabit 24 Port Managed sedang dilakukan pemeriksaan daya port PoE.',
            ],
            [
                'asset_code'    => 'AST-005',
                'name'          => 'Server Dell R730',
                'category_name' => 'Server',
                'serial_number' => 'SVR001567',
                'location_name' => 'Ruang Server',
                'wh_code'       => null,
                'status'        => 'Aktif',
                'vendor'        => 'Dell',
                'purchase_date' => '2023-01-05',
                'warranty'      => '36 Bulan',
                'specs'         => '2x Intel Xeon E5-2680 v4, 128GB DDR4 ECC, 4x 1.2TB SAS 10K RAID 10',
                'icon'          => 'database',
                'description'   => 'Server rackmount 2U untuk sistem aplikasi operasional dan basis data.',
            ],
            [
                'asset_code'    => 'AST-006',
                'name'          => 'Laptop Technician',
                'category_name' => 'Laptop',
                'serial_number' => 'LAP001890',
                'location_name' => 'Gudang Utama',
                'wh_code'       => 'WH-001',
                'status'        => 'Aktif',
                'vendor'        => 'Lenovo',
                'purchase_date' => '2024-05-12',
                'warranty'      => '24 Bulan',
                'specs'         => 'Intel Core i5-12450H, 16GB DDR4, 512GB SSD NVMe, RJ45 Gigabit',
                'icon'          => 'laptop',
                'description'   => 'Laptop operasional teknisi lapangan untuk commissioning OLT dan analisis sinyal.',
            ],
            [
                'asset_code'    => 'AST-007',
                'name'          => 'Kabel FO 12 Core',
                'category_name' => 'Kabel',
                'serial_number' => 'FOO001234',
                'location_name' => 'Gudang Besuki',
                'wh_code'       => 'WH-003',
                'status'        => 'Aktif',
                'vendor'        => 'Fiberhome',
                'purchase_date' => '2024-06-18',
                'warranty'      => '6 Bulan',
                'specs'         => 'Single Mode G.652D, Loose Tube, Kawat Baja Seling Kuat',
                'icon'          => 'cable',
                'description'   => 'Kabel fiber optik 12 core outdoor aerial untuk jalur backbone distribusi.',
            ],
            [
                'asset_code'    => 'AST-008',
                'name'          => 'ODP Box',
                'category_name' => 'Perangkat FO',
                'serial_number' => 'ODP001567',
                'location_name' => 'Lokasi Lapangan',
                'wh_code'       => null,
                'status'        => 'Rusak',
                'vendor'        => 'Fiberstar',
                'purchase_date' => '2023-08-08',
                'warranty'      => 'Expired',
                'specs'         => '16 Core Optical Distribution Box, IP65 Waterproof Lock',
                'icon'          => 'box',
                'description'   => 'Box ODP pole-mount retak fisik akibat tertimpa dahan pohon, dalam antrean penggantian.',
            ],
            [
                'asset_code'    => 'AST-009',
                'name'          => 'Tool Kit',
                'category_name' => 'Peralatan',
                'serial_number' => 'TKT001890',
                'location_name' => 'Gudang Utama',
                'wh_code'       => 'WH-001',
                'status'        => 'Aktif',
                'vendor'        => 'Jonard Tools',
                'purchase_date' => '2024-02-22',
                'warranty'      => '12 Bulan',
                'specs'         => 'Optical Power Meter, VFL 10mW, Stripper CFS-2, Precision Cleaver',
                'icon'          => 'wrench',
                'description'   => 'Satu set toolkit penyambungan fiber optik, cleaver presisi, dan visual fault locator.',
            ],
            [
                'asset_code'    => 'AST-010',
                'name'          => 'Kamera CCTV',
                'category_name' => 'Keamanan',
                'serial_number' => 'CCTV001234',
                'location_name' => 'Kantor',
                'wh_code'       => null,
                'status'        => 'Aktif',
                'vendor'        => 'Hikvision',
                'purchase_date' => '2023-11-11',
                'warranty'      => '24 Bulan',
                'specs'         => '4MP IP Bullet Camera, 30m IR Night Vision, PoE, IP67 Weatherproof',
                'icon'          => 'video',
                'description'   => 'Kamera IP surveillance outdoor 4MP untuk pengawasan keamanan pintu masuk gudang.',
            ],
        ];

        $assets = [];
        foreach ($assetsData as $aData) {
            $cat = $categories[$aData['category_name']] ?? null;
            $wh = !empty($aData['wh_code']) && isset($warehouses[$aData['wh_code']]) ? $warehouses[$aData['wh_code']] : null;

            $asset = Asset::updateOrCreate(
                ['asset_code' => $aData['asset_code']],
                [
                    'name'          => $aData['name'],
                    'category_id'   => $cat ? $cat->id : null,
                    'serial_number' => $aData['serial_number'],
                    'warehouse_id'  => $wh ? $wh->id : null,
                    'location_name' => $aData['location_name'],
                    'status'        => $aData['status'],
                    'vendor'        => $aData['vendor'],
                    'purchase_date' => $aData['purchase_date'],
                    'warranty'      => $aData['warranty'],
                    'specs'         => $aData['specs'],
                    'icon'          => $aData['icon'],
                    'description'   => $aData['description'],
                ]
            );
            $assets[$aData['asset_code']] = $asset;
        }

        // 8. TICKETS & RELASI
        $ticketsData = [
            [
                'ticket_number' => 'TK-1025',
                'customer_name' => 'Arkanet',
                'cust_code'     => 'CST0001',
                'location'      => 'Situbondo',
                'title'         => 'Replace ONT',
                'subtitle'      => 'Ganti ONT ZTE F670L',
                'priority'      => 'High',
                'status'        => 'Open',
                'tech'          => 'Budi Santoso',
                'asset_code'    => 'AST-001',
                'description'   => 'Ganti ONT ZTE F670L yang mengalami kerusakan. Pelanggan sudah melaporkan sejak pagi.',
                'sla_hours'     => 4,
                'materials'     => [
                    ['name' => 'ONT ZTE F670L', 'qty' => 1, 'unit' => 'Unit'],
                    ['name' => 'Fiber Patchcord 3m', 'qty' => 1, 'unit' => 'Pcs'],
                ],
                'attachments'   => [
                    ['file_name' => 'foto_ont_los.jpg', 'file_size' => 1468006, 'file_size_human' => '1.4 MB'],
                ],
            ],
            [
                'ticket_number' => 'TK-1024',
                'customer_name' => 'PT ABC',
                'cust_code'     => 'CST0002',
                'location'      => 'Besuki',
                'title'         => 'Internet Down',
                'subtitle'      => 'Internet kantor mati total',
                'priority'      => 'High',
                'status'        => 'Resolved',
                'tech'          => 'Ahmad Fauzi',
                'asset_code'    => 'AST-001',
                'description'   => 'Koneksi internet kantor mati total sejak pagi. Teknisi telah menyambung kabel drop fiber yang putus.',
                'sla_hours'     => 4,
                'materials'     => [
                    ['name' => 'Protection Sleeve', 'qty' => 2, 'unit' => 'Pcs'],
                ],
                'attachments'   => [
                    ['file_name' => 'laporan_pekerjaan.pdf', 'file_size' => 552960, 'file_size_human' => '540 KB'],
                ],
            ],
            [
                'ticket_number' => 'TK-1023',
                'customer_name' => 'Office',
                'cust_code'     => 'CST0003',
                'location'      => 'Asembagus',
                'title'         => 'Router Problem',
                'subtitle'      => 'Router sering restart sendiri',
                'priority'      => 'Normal',
                'status'        => 'In Progress',
                'tech'          => 'Dimas Priyanto',
                'asset_code'    => 'AST-003',
                'description'   => 'Router sering hang dan perlu direstart manual beberapa kali sehari saat beban kerja tinggi.',
                'sla_hours'     => 8,
                'materials'     => [],
                'attachments'   => [],
            ],
            [
                'ticket_number' => 'TK-1022',
                'customer_name' => 'Customer #2393',
                'cust_code'     => 'CST0004',
                'location'      => 'Banyuglugur',
                'title'         => 'No Signal',
                'subtitle'      => 'Lampu indikator LOS merah',
                'priority'      => 'Normal',
                'status'        => 'Assigned',
                'tech'          => 'Rizky Maulana',
                'asset_code'    => 'AST-002',
                'description'   => 'Lampu LOS menyala merah, tidak ada sinyal dari core kabel fiber optik pelanggan.',
                'sla_hours'     => 8,
                'materials'     => [],
                'attachments'   => [],
            ],
            [
                'ticket_number' => 'TK-1021',
                'customer_name' => 'Cafe 99',
                'cust_code'     => 'CST0005',
                'location'      => 'Situbondo',
                'title'         => 'Speed Low',
                'subtitle'      => 'Kecepatan jauh di bawah paket',
                'priority'      => 'Low',
                'status'        => 'Open',
                'tech'          => null,
                'asset_code'    => 'AST-001',
                'description'   => 'Kecepatan internet drop saat ramai pelanggan, terutama di jam sibuk sore hari.',
                'sla_hours'     => 24,
                'materials'     => [],
                'attachments'   => [],
            ],
            [
                'ticket_number' => 'TK-1020',
                'customer_name' => 'PT Sinar',
                'cust_code'     => 'CST0006',
                'location'      => 'Besuki',
                'title'         => 'Network Problem',
                'subtitle'      => 'Packet loss tinggi di switch lantai 2',
                'priority'      => 'High',
                'status'        => 'Waiting',
                'tech'          => 'Budi Santoso',
                'asset_code'    => 'AST-004',
                'description'   => 'Jaringan kantor tersendat. Menunggu pengiriman kabel patchcord baru dari gudang pusat.',
                'sla_hours'     => 8,
                'materials'     => [],
                'attachments'   => [],
            ],
            [
                'ticket_number' => 'TK-1019',
                'customer_name' => 'Hotel Indah',
                'cust_code'     => 'CST0007',
                'location'      => 'Asembagus',
                'title'         => 'WiFi Down',
                'subtitle'      => 'Access Point area lobby mati',
                'priority'      => 'Normal',
                'status'        => 'Resolved',
                'tech'          => 'Ahmad Fauzi',
                'asset_code'    => null,
                'description'   => 'WiFi area lobby tidak bisa diakses tamu hotel. Adaptor PoE diganti baru dan access point normal.',
                'sla_hours'     => 8,
                'materials'     => [
                    ['name' => 'PoE Injector 24V', 'qty' => 1, 'unit' => 'Pcs'],
                ],
                'attachments'   => [],
            ],
            [
                'ticket_number' => 'TK-1018',
                'customer_name' => 'UD Maju',
                'cust_code'     => 'CST0008',
                'location'      => 'Situbondo',
                'title'         => 'Perangkat Rusak',
                'subtitle'      => 'ONT mati total kena petir',
                'priority'      => 'Critical',
                'status'        => 'Open',
                'tech'          => 'Dimas Priyanto',
                'asset_code'    => 'AST-002',
                'description'   => 'Perangkat utama mati total setelah hujan petir kemarin sore. Seluruh transaksi kasir terhambat.',
                'sla_hours'     => 2,
                'materials'     => [],
                'attachments'   => [],
            ],
            [
                'ticket_number' => 'TK-1017',
                'customer_name' => 'Klinik Sehat',
                'cust_code'     => 'CST0009',
                'location'      => 'Besuki',
                'title'         => 'Internet Lambat',
                'subtitle'      => 'Akses rekam medis lambat',
                'priority'      => 'Normal',
                'status'        => 'Assigned',
                'tech'          => 'Rizky Maulana',
                'asset_code'    => 'AST-001',
                'description'   => 'Akses sistem rekam medis BPJS sangat lambat pada jam pelayanan pasien.',
                'sla_hours'     => 8,
                'materials'     => [],
                'attachments'   => [],
            ],
            [
                'ticket_number' => 'TK-1016',
                'customer_name' => 'Koperasi ABC',
                'cust_code'     => 'CST0010',
                'location'      => 'Banyuglugur',
                'title'         => 'Ganti Kabel',
                'subtitle'      => 'Kabel luar ruangan terputus',
                'priority'      => 'Low',
                'status'        => 'Closed',
                'tech'          => 'Fajar Ramadhan',
                'asset_code'    => 'AST-007',
                'description'   => 'Kabel dropcore 1 core putus terkena dahan pohon. Sudah dilakukan penarikan kabel baru 85 meter.',
                'sla_hours'     => 24,
                'materials'     => [
                    ['name' => 'Kabel Dropcore 1C', 'qty' => 85, 'unit' => 'Meter'],
                ],
                'attachments'   => [],
            ],
        ];

        foreach ($ticketsData as $tData) {
            $cust = isset($customers[$tData['cust_code']]) ? $customers[$tData['cust_code']] : null;
            $ast = !empty($tData['asset_code']) && isset($assets[$tData['asset_code']]) ? $assets[$tData['asset_code']] : null;

            $ticket = Ticket::updateOrCreate(
                ['ticket_number' => $tData['ticket_number']],
                [
                    'customer_id'        => $cust ? $cust->id : null,
                    'customer_name'      => $tData['customer_name'],
                    'asset_id'           => $ast ? $ast->id : null,
                    'created_by'         => $adminUser->id,
                    'assigned_tech_name' => $tData['tech'],
                    'title'              => $tData['title'],
                    'subtitle'           => $tData['subtitle'],
                    'description'        => $tData['description'],
                    'location'           => $tData['location'],
                    'priority'           => $tData['priority'],
                    'status'             => $tData['status'],
                    'sla_due_at'         => Carbon::now()->addHours($tData['sla_hours']),
                ]
            );

            // Assign teknisi jika ada
            if (!empty($tData['tech']) && isset($technicians[$tData['tech']])) {
                TicketAssignment::updateOrCreate(
                    [
                        'ticket_id'     => $ticket->id,
                        'technician_id' => $technicians[$tData['tech']]->id,
                    ],
                    [
                        'assigned_by' => $adminUser->id,
                        'assigned_at' => Carbon::now()->subHours(2),
                        'is_active'   => !in_array($ticket->status, ['Closed', 'Resolved']),
                    ]
                );
            }

            // Material
            foreach ($tData['materials'] as $mat) {
                TicketMaterial::firstOrCreate([
                    'ticket_id' => $ticket->id,
                    'item_name' => $mat['name'],
                    'quantity'  => $mat['qty'],
                    'unit'      => $mat['unit'],
                ]);
            }

            // Attachment
            foreach ($tData['attachments'] as $att) {
                TicketAttachment::firstOrCreate([
                    'ticket_id'        => $ticket->id,
                    'file_name'        => $att['file_name'],
                    'file_path'        => 'attachments/' . $att['file_name'],
                    'file_size'        => $att['file_size'],
                    'file_size_human'  => $att['file_size_human'],
                ]);
            }
        }

        // 9. RIWAYAT PERGERAKAN STOK (STOCK MOVEMENTS)
        $movementsData = [
            ['sku' => 'PRD-001', 'wh' => 'WH-001', 'type' => 'Keluar', 'quantity' => 1,  'reference' => 'Ticket #TK-1025', 'user_name' => 'Budi Santoso'],
            ['sku' => 'PRD-001', 'wh' => 'WH-001', 'type' => 'Masuk',  'quantity' => 50, 'reference' => 'PO-2026-089',   'user_name' => 'Eka Pratama'],
            ['sku' => 'PRD-001', 'wh' => 'WH-001', 'type' => 'Keluar', 'quantity' => 2,  'reference' => 'Ticket #TK-1018', 'user_name' => 'Dimas Priyanto'],
            ['sku' => 'PRD-002', 'wh' => 'WH-002', 'type' => 'Keluar', 'quantity' => 1,  'reference' => 'Ticket #TK-1022', 'user_name' => 'Rizky Maulana'],
            ['sku' => 'PRD-003', 'wh' => 'WH-001', 'type' => 'Keluar', 'quantity' => 20, 'reference' => 'Maintenance Jaringan', 'user_name' => 'Ahmad Fauzi'],
            ['sku' => 'PRD-004', 'wh' => 'WH-003', 'type' => 'Keluar', 'quantity' => 1,  'reference' => 'Ticket #TK-1025', 'user_name' => 'Budi Santoso'],
            ['sku' => 'PRD-006', 'wh' => 'WH-001', 'type' => 'Masuk',  'quantity' => 100, 'reference' => 'PO-2026-077',   'user_name' => 'Eka Pratama'],
        ];

        foreach ($movementsData as $m) {
            if (isset($items[$m['sku']]) && isset($warehouses[$m['wh']])) {
                StockMovement::create([
                    'inventory_item_id' => $items[$m['sku']]->id,
                    'warehouse_id'      => $warehouses[$m['wh']]->id,
                    'type'              => $m['type'],
                    'quantity'          => $m['quantity'],
                    'reference'         => $m['reference'],
                    'user_name'         => $m['user_name'],
                ]);
            }
        }

        // 10. SYSTEM SETTINGS
        $settingsData = [
            ['group' => 'general',      'key' => 'company.name',    'value' => 'PT NexOps Solusindo'],
            ['group' => 'general',      'key' => 'company.email',   'value' => 'support@nexops.id'],
            ['group' => 'general',      'key' => 'company.phone',   'value' => '(0338) 671234'],
            ['group' => 'general',      'key' => 'company.address', 'value' => 'Jl. PB Sudirman No. 88, Situbondo, Jawa Timur'],
            ['group' => 'system',       'key' => 'app.timezone',    'value' => 'Asia/Jakarta'],
            ['group' => 'system',       'key' => 'app.currency',    'value' => 'IDR'],
            ['group' => 'notification', 'key' => 'alert.low_stock', 'value' => 'true'],
            ['group' => 'notification', 'key' => 'alert.sla_due',   'value' => 'true'],
            ['group' => 'security',     'key' => 'auth.2fa',        'value' => 'optional'],
        ];

        foreach ($settingsData as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }

        // 11. LOGIN HISTORIES & ACTIVITY LOGS
        LoginHistory::create([
            'user_id'      => $adminUser->id,
            'ip_address'   => '127.0.0.1',
            'user_agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/128.0.0.0',
            'device'       => 'Desktop (Windows)',
            'location'     => 'Situbondo, Indonesia',
            'status'       => 'Success',
            'logged_in_at' => Carbon::now()->subMinutes(15),
        ]);

        ActivityLog::create([
            'user_id'     => $adminUser->id,
            'user_name'   => $adminUser->name,
            'action'      => 'system_migration',
            'description' => 'Migrasi struktur database dan seeding data operasional NexOps selesai dilakukan.',
        ]);
    }
}
