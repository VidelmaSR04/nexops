# Panduan Pengerjaan Tim & Standar Teknis NEXOPS

Dokumen ini adalah acuan kerja untuk **Kamu (Lead)**, **Teman 1**, dan **Teman 2** berdasarkan bagan *Pembagian Jobdesk Project NEXOPS*.

---

## 1. Pembagian Tanggung Jawab & File Kerja

| Anggota Tim | Peran | Modul Tanggung Jawab | File Controller & Route Terkait |
| :--- | :--- | :--- | :--- |
| **Kamu (Lead Developer)** | Frontend Utama + Backend Inti & API | • Tickets (CRUD, SLA, status, material)<br>• Dashboard (Statistik operasional riil)<br>• Layout & Reusable Component Blade<br>• Koordinasi Arsitektur & Git | `TicketController.php`<br>`DashboardController.php`<br>`SettingController.php`<br>`resources/views/layouts/*` |
| **Teman 1** | Backend Modul Operasional | • Inventory (CRUD barang, kategori, minimum stok)<br>• Warehouse (CRUD gudang, lokasi, kapasitas)<br>• Assets (CRUD aset, serial number, garansi)<br>• Validasi pergerakan stok (masuk/keluar) | `InventoryController.php`<br>`WarehouseController.php`<br>`AssetController.php`<br>`StockMovement.php` |
| **Teman 2** | Backend Pengguna Operasional | • Technicians (CRUD teknisi, status, rating)<br>• Customers (CRUD pelanggan, paket, tagihan)<br>• Ticket Assignment (penugasan teknisi)<br>• Activity Log (pencatatan aktivitas) | `TechnicianController.php`<br>`CustomerController.php`<br>`ActivityLog.php`<br>`TicketAssignment.php` |

---

## 2. Cara Cepat Setup Project di Laptop Teman 1 & Teman 2

Kirimkan langkah ini ke Teman 1 dan Teman 2:

1. **Clone / Buka Folder Project** di Laragon (`C:\laragon\www\nexops`).
2. **Install Dependency**:
   ```bash
   composer install
   npm install
   ```
3. **Konfigurasi Environment**:
   - Salin file `.env.example` menjadi `.env` (atau `copy .env.example .env`).
   - Pastikan database di `.env` adalah:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=nexops1
     DB_USERNAME=root
     DB_PASSWORD=
     ```
   - Generate App Key:
     ```bash
     php artisan key:generate
     ```
4. **Setup Database di Laragon (phpMyAdmin)**:
   - Pastikan MySQL Laragon sudah **Start All**.
   - Buat database baru bernama **`nexops1`** di phpMyAdmin (`http://localhost/phpmyadmin`).
5. **Migrate & Seed Otomatis**:
   - Jalankan perintah ini di terminal:
     ```bash
     php artisan migrate --seed
     ```
   - *Semua tabel (24 tabel) dan data prototype UI langsung otomatis masuk ke database MySQL.*
6. **Jalankan Aplikasi**:
   ```bash
   npm run dev
   php artisan serve
   ```
   Buka browser di: `http://localhost:8000` atau link virtual host Laragon (`http://nexops.test`).

---

## 3. Standar Format Response REST API (Disepakati)

Untuk Teman 1 dan Teman 2 yang membuat endpoint API di `routes/api.php`, gunakan format response JSON yang seragam:

### Format Sukses:
```json
{
  "success": true,
  "message": "Data berhasil disimpan.",
  "data": {
    "id": 1,
    "name": "ONT ZTE F670L"
  }
}
```

### Format Gagal / Validasi Error:
```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "name": ["Nama barang wajib diisi."]
  }
}
```

---

## 4. Aturan Git / Pembagian Branch Agar Tidak Bentrok (Conflict)

1. **Branch Utama**: `main` (hanya untuk kode yang sudah stabil dan diuji).
2. **Branch Kerja Masing-Masing**:
   - Kamu: `feature/tickets-dashboard`
   - Teman 1: `feature/inventory-warehouse-assets`
   - Teman 2: `feature/technicians-customers`
3. **Aturan Penting**:
   - Jangan mengubah migration yang sudah ada (`2026_10_12_084058_create_nexops_tables.php`). Model dan kolomnya sudah lengkap mencakup seluruh UI.
   - Teman 1 fokus mengisi method store/update/destroy di `InventoryController`, `WarehouseController`, dan `AssetController`.
   - Teman 2 fokus mengisi method store/update/destroy di `CustomerController` dan `TechnicianController`.

