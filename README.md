<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Requirements

Sebelum memulai, pastikan sistem Anda memiliki:

- PHP >= 8.2
- Composer
- MySQL atau database lain yang didukung
- Node.js >= 18 + NPM
- Git
- Laravel CLI
- Ekstensi PHP:
  - `pdo`
  - `mbstring`
  - `openssl`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `bcmath`
  - `fileinfo`

---

## 🚀 Instalasi

```bash
# 1. Clone repository
git clone https://github.com/username/nama-repo.git
cd nama-repo

# 2. Install dependency PHP
composer install

# 3. Install dependency frontend
npm install && npm run build

# 4. Salin file konfigurasi .env
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Atur konfigurasi database di file .env

# 7. Jalankan migrasi database
php artisan migrate

# 8. (Opsional) Isi data awal
php artisan db:seed

# 9. Jalankan server lokal
php artisan serve
