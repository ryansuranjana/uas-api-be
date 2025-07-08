# UAS API Backend

## Anggota Kelompok

-   I Putu Ryan Suranjana (230040056)
-   I Gede Eka Purwadi (230040087)
-   I Nyoman Yudistira Hady Winanda (230040092)
-   Pedrof Da Kristof (230040071)

## Tech Stack

Lumen API

## Setup

Clone dari github / atau bisa didownload lewat zip

```bash
git clone git@github.com:ryansuranjana/uas-api-be.git
```

Install dependency

```bash
composer install
```

Buat file .env

```bash
cp .env.example .env
```

Konfigurasi database di .env bagian ini

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Jalankan migration database

```bash
php artisan migrate
```

Jalankan seeder database

```bash
php artisan db:seed
```

Jalankan aplikasi

```bash
php -S localhost:8000 -t public
```

Jika ingin melihat dokumentasi api bisa akses ke endpoint ini

```http
  GET /api/documentation
```

## Akun
email: superadmin@gmail.com <br>
password: password
