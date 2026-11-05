# NEXOPS — Operations Management System

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Integrated Operations & Ticketing Management System</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Blade-Templates-F05340" alt="Laravel Blade">
  <img src="https://img.shields.io/badge/Database-MySQL-4479A1?logo=mysql&logoColor=white" alt="MySQL">
</p>

## About NEXOPS

**NEXOPS (Operations Management System)** adalah aplikasi web yang dirancang untuk membantu pengelolaan operasional dan ticketing dalam satu sistem terintegrasi.

NEXOPS dikembangkan menggunakan Laravel 12 dengan Laravel Blade sebagai template antarmuka dan MySQL sebagai database. Project ini berfokus pada pengelolaan tiket, inventaris, aset, gudang, teknisi, pelanggan, dan laporan operasional.

## Features

- **Dashboard** — Menampilkan ringkasan informasi dan statistik operasional.
- **Ticket Management** — Mengelola tiket, prioritas, status, dan penugasan tiket.
- **Inventory Management** — Mengelola barang, kategori, dan stok inventaris.
- **Asset Management** — Mengelola data aset, nomor seri, kondisi, dan status.
- **Warehouse Management** — Mengelola data gudang, lokasi, dan pergerakan stok.
- **Technician Management** — Mengelola data teknisi dan informasi ketersediaannya.
- **Customer Management** — Mengelola informasi dan data pelanggan.
- **Reports** — Menyediakan ringkasan laporan operasional.
- **Settings** — Menyediakan pengaturan aplikasi sesuai kebutuhan.

*Status implementasi setiap fitur mengikuti perkembangan project.*

## Tech Stack

| Technology | Description |
|---|---|
| PHP 8.2 | Backend programming language |
| Laravel 12 | Web application framework and REST API |
| Laravel Blade | Frontend templating engine |
| HTML, CSS, JavaScript | Frontend development |
| MySQL | Relational database |
| phpMyAdmin | Database management |
| Postman | REST API testing |
| Git & GitHub | Version control and collaboration |
| Visual Studio Code | Code editor |

## Requirements

Pastikan perangkat telah memiliki:

- PHP 8.2 atau versi yang memenuhi persyaratan Laravel 12.
- Composer.
- MySQL.
- Git.
- Web browser.
- phpMyAdmin atau database management tool lainnya.

Laragon dapat digunakan sebagai lingkungan pengembangan lokal pada Windows.

## Installation

### 1. Clone Repository

```bash
git clone <URL_REPOSITORY_GITHUB>
cd NEXOPS
```

Ganti `<URL_REPOSITORY_GITHUB>` dengan URL repository NEXOPS yang sebenarnya.

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

Salin file environment contoh menggunakan PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Jika file `.env` sudah tersedia, periksa konfigurasi yang ada sebelum membuat atau menggantinya.

### 4. Create MySQL Database

Buka phpMyAdmin, lalu buat database baru dengan nama:

```text
nexops
```

Nama database dapat disesuaikan dengan konfigurasi project.

### 5. Configure Database Connection

Sesuaikan pengaturan database di file `.env`:

```env
APP_NAME=NEXOPS
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexops
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan username dan password dengan konfigurasi MySQL lokal masing-masing.

**Security note:** Jangan mengunggah file `.env`, password database, atau kredensial rahasia ke repository publik.

### 6. Run Database Migrations

Setelah database dan konfigurasi selesai disiapkan, jalankan:

```bash
php artisan migrate
```

Pastikan migration yang diperlukan telah tersedia. Jika project memiliki seeder, jalankan sesuai kebutuhan untuk mengisi data awal.

#### Optional: Demo Tickets

After the OPD and technician master data is available, add four repeatable demo tickets without creating duplicate customers:

```bash
php artisan db:seed --class=DemoTicketSeeder
```

This seeder is optional and is not included in the default `db:seed` command. Do not run it against production data.

### 7. Start the Development Server

```bash
php artisan serve
```

Buka aplikasi melalui browser:

```text
http://127.0.0.1:8000
```

## REST API

NEXOPS menggunakan Laravel untuk pengembangan REST API guna mendukung pengelolaan dan integrasi data antarmodul.

Contoh rancangan endpoint ticketing:

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/tickets` | Retrieve ticket list |
| GET | `/api/tickets/{id}` | Retrieve ticket details |
| POST | `/api/tickets` | Create a ticket |
| PUT | `/api/tickets/{id}` | Update a ticket |
| DELETE | `/api/tickets/{id}` | Delete a ticket |

Endpoint di atas merupakan contoh rancangan. Implementasi aktual mengikuti definisi route pada `routes/api.php`.

REST API dapat diuji menggunakan Postman sebelum diintegrasikan dengan halaman Blade.

## Team Responsibilities

NEXOPS dikembangkan oleh tim yang terdiri dari tiga anggota.

### 1. Lead Developer — Web System Design & API Developer

- Merancang dan mengimplementasikan UI/UX seluruh halaman web.
- Mengembangkan frontend menggunakan Laravel Blade.
- Menjaga konsistensi layout, navigasi, dan komponen antarmuka.
- Mengembangkan backend Tickets dan Dashboard.
- Menetapkan standar REST API dan integrasi antarmodul.
- Mengoordinasikan code review, integrasi, dan pengujian sistem.

### 2. Backend Developer — Inventory, Warehouse & Assets

- Mengembangkan backend Inventory Management.
- Mengelola data gudang dan pergerakan stok.
- Mengembangkan backend Asset Management.
- Membuat migration, model, controller, validasi, dan endpoint API untuk modul terkait.

### 3. Backend Developer — Technicians, Customers & Assignment

- Mengembangkan backend Technician Management.
- Mengelola data pelanggan.
- Mengembangkan fitur penugasan tiket.
- Membuat migration, model, controller, validasi, dan endpoint API untuk modul terkait.

Seluruh anggota bertanggung jawab untuk menguji dan mengintegrasikan modul masing-masing sesuai standar project.

## GitHub Workflow

Untuk mendukung kolaborasi tim, setiap anggota menggunakan branch terpisah saat mengembangkan fitur.

Contoh alur kerja:

```bash
git checkout -b feature/nama-fitur
git add .
git commit -m "feat: implement nama fitur"
git push -u origin feature/nama-fitur
```

Workflow tim:

1. Ambil perubahan terbaru dari branch utama.
2. Buat branch untuk fitur yang dikerjakan.
3. Implementasikan dan uji perubahan secara lokal.
4. Push branch ke GitHub.
5. Buat Pull Request untuk code review.
6. Gabungkan perubahan setelah disetujui dan diuji.

## Testing

Pengujian dilakukan secara bertahap, meliputi:

- REST API testing menggunakan Postman.
- CRUD dan validasi form.
- Relasi database MySQL.
- Pengelolaan tiket dan penugasan teknisi.
- Pengelolaan stok, gudang, dan aset.
- Hak akses pengguna.
- Integrasi backend dengan halaman Blade.
- Pengujian tampilan responsif.

## Development Status

Frontend Blade telah selesai dibuat dan dapat dibuka. Tahap pengembangan selanjutnya berfokus pada perancangan database, pengembangan backend per modul, implementasi REST API, integrasi data, dan pengujian sistem.

## License

Lisensi project mengikuti ketentuan yang ditetapkan oleh pemilik repository.

---

<p align="center">
  <strong>NEXOPS — Operations Management System</strong>
  <br>
  <em>One System, Better Operations.</em>
</p>
