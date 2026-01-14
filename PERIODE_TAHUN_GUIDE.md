# Dokumentasi: Filtering Data Berdasarkan Periode Tahun

## 📋 Ringkasan Implementasi

Sistem ini memungkinkan setiap user memilih periode tahun (2025 atau 2026) saat login. Semua data yang ditampilkan akan otomatis di-filter berdasarkan periode tahun yang dipilih.

---

## 🔧 Komponen-Komponen Utama

### 1. **Login Form dengan Pilihan Periode Tahun**
- **File**: [app/Filament/Auth/CustomLogin.php](app/Filament/Auth/CustomLogin.php)
- **Fitur**: 
  - Menambahkan field `Select` untuk pilihan tahun (2025, 2026)
  - Menyimpan pilihan ke database (`users.periode_tahun`) dan session
  - Otomatis dijalankan saat user login

### 2. **Global Scope untuk Auto-Filtering**
- **File**: [app/Traits/FilterByPeriodeTahun.php](app/Traits/FilterByPeriodeTahun.php)
- **Cara Kerja**: 
  - Trait ini menambahkan global scope pada model
  - Setiap query otomatis ditambahkan `WHERE periode_tahun = {selected_year}`
  - Hanya bisa dihapus dengan `withoutGlobalScopes()` jika diperlukan

### 3. **Helper Functions**
- **File**: [app/Helpers/PeriodeTahunHelper.php](app/Helpers/PeriodeTahunHelper.php)
- **Fungsi**:
  - `getPeriodeTahun()` - Mendapatkan periode tahun dari session
  - `hasPeriodeTahun()` - Cek apakah user sudah memilih periode tahun

### 4. **Middleware untuk Sinkronisasi Session**
- **File**: [app/Http/Middleware/SetPeriodeTahunFromAuth.php](app/Http/Middleware/SetPeriodeTahunFromAuth.php)
- **Fungsi**: Otomatis set `session['periode_tahun']` dari database user setiap request

---

## 📊 Model yang Menggunakan FilterByPeriodeTahun

Trait `FilterByPeriodeTahun` sudah ditambahkan ke model:

1. ✅ `LogBarangMasuk` - Log pemasukan barang
2. ✅ `LogBarangKeluar` - Log pengeluaran barang
3. ✅ `Permintaan` - Data permintaan barang
4. ✅ `PermintaanItems` - Item dalam permintaan
5. ✅ `LogPermintaan` - Log status permintaan

Semua model ini secara otomatis akan memfilter data berdasarkan `periode_tahun` di session.

---

## 🎯 Flow Lengkap (User Journey)

```
1. User membuka halaman login
   ↓
2. Masukkan username/email dan password
   ↓
3. Pilih periode tahun dari dropdown (2025 atau 2026)
   ↓
4. Klik Login
   ↓
5. Sistem memvalidasi kredensial
   ↓
6. Jika valid, simpan periode_tahun ke:
   - users.periode_tahun (database)
   - session['periode_tahun'] (session)
   ↓
7. User diarahkan ke dashboard
   ↓
8. Setiap kali ambil data (query), otomatis filter:
   WHERE periode_tahun = session['periode_tahun']
   ↓
9. User hanya melihat data sesuai periode tahun yang dipilih
```

---

## 💾 Data Flow Penyimpanan

### Saat Login
```php
// CustomLogin.php - authenticate() method
$user->update(['periode_tahun' => $data['periode_tahun']]);  // Simpan ke database
session(['periode_tahun' => $data['periode_tahun']]);         // Simpan ke session
```

### Setiap Request
```php
// SetPeriodeTahunFromAuth.php middleware
if (auth()->check() && auth()->user()->periode_tahun) {
    session(['periode_tahun' => auth()->user()->periode_tahun]);
}
```

### Saat Query
```php
// FilterByPeriodeTahun.php trait
// Otomatis menambahkan:
WHERE periode_tahun = session['periode_tahun']

// Contoh:
LogBarangMasuk::all();  // Hanya data dengan periode_tahun = session value
```

---

## 📝 Contoh Penggunaan

### 1. Query Biasa (Sudah Terfilter)
```php
// Semua query otomatis terfilter berdasarkan periode_tahun user
$logsIn = LogBarangMasuk::all();
// Hasil: Hanya data tahun 2025 (jika user memilih 2025)

$permintaan = Permintaan::where('status', 'approved')->get();
// Hasil: Hanya permintaan tahun 2025 dengan status approved
```

### 2. Query Tanpa Filter (Jika Perlu Lihat Semua Data)
```php
// Gunakan withoutGlobalScopes() untuk menghapus filter
$allLogs = LogBarangMasuk::withoutGlobalScopes()->get();
// Hasil: Semua data tanpa filter periode tahun
```

### 3. Pada Blade Template
```blade
<!-- Tampilkan periode tahun yang sedang aktif -->
<p>Data Periode: {{ getPeriodeTahun() }}</p>

<!-- Cek apakah user sudah memilih periode -->
@if(hasPeriodeTahun())
    <p>Periode tahun: {{ getPeriodeTahun() }}</p>
@endif
```

### 4. Pada Form (Filament Resource)
```php
protected static ?string $model = LogBarangMasuk::class;

protected function getFormSchema(): array
{
    return [
        // ... field lainnya
        Hidden::make('periode_tahun')
            ->default(fn() => getPeriodeTahun()),
    ];
}
```

---

## 🔄 Perubahan Database

Semua tabel yang memerlukan filtering sudah memiliki kolom `periode_tahun`:

```
Migration: 2026_01_14_095804_add_periode_tahun_to_barang_table.php

Tabel yang diupdate:
- barang
- log_barang_masuk
- log_barang_keluar
- log_permintaan
- permintaan_checkout
- permintaan_checkout_items
```

---

## ⚙️ Konfigurasi Sistem

### Middleware Registration
File: `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\SetPeriodeTahunFromAuth::class,
    ]);
})
```

### Autoload Helper
File: `composer.json`
```json
"files": [
    "app/Helpers/PeriodeTahunHelper.php"
]
```

---

## 📌 Important Notes

### 1. Logika Filter di Trait
Filter hanya berlaku ketika ada nilai di `session['periode_tahun']`. Jika session kosong, semua data ditampilkan.

### 2. Persist Setelah Logout
Ketika user logout, session akan dihapus. Saat login kembali, user harus memilih periode tahun lagi.

### 3. Multi-Device Login
Jika user login dari device berbeda dengan periode tahun berbeda, hanya session device itu yang terpengaruh. Database `users.periode_tahun` akan ter-update ke pilihan terakhir.

### 4. SuperAdmin Akses Semua Data
Jika SuperAdmin perlu melihat semua data tanpa filter:
```php
// Gunakan withoutGlobalScopes()
LogBarangMasuk::withoutGlobalScopes()->get();
```

---

## 🧪 Testing

### Manual Testing Steps

#### Step 1: Cek Login Form
1. Buka `http://localhost:8000/bpkp/login`
2. Pastikan ada field dropdown "Periode Tahun"
3. Pilih tahun dan login

#### Step 2: Cek Database
```sql
SELECT id, name, periode_tahun FROM users WHERE id = 1;
```
Pastikan `periode_tahun` berisi tahun yang dipilih

#### Step 3: Cek Data yang Ditampilkan
1. Login dengan periode 2025
2. Buka halaman Log Barang Masuk
3. Catat record yang ditampilkan
4. Logout, login dengan periode 2026
5. Buka halaman yang sama
6. Data harus berbeda (hanya tahun 2026)

#### Step 4: Cek Session
Buka tinker:
```bash
php artisan tinker
```

```php
session('periode_tahun')  // Seharusnya menampilkan tahun yang dipilih
getPeriodeTahun()         // Helper function
```

---

## 📂 File Structure

```
app/
├── Filament/Auth/
│   └── CustomLogin.php                    ✏️ MODIFIED
├── Helpers/
│   └── PeriodeTahunHelper.php             🆕 NEW
├── Http/Middleware/
│   └── SetPeriodeTahunFromAuth.php        🆕 NEW
├── Models/
│   ├── User.php                           ✏️ MODIFIED
│   ├── LogBarangMasuk.php                 ✏️ MODIFIED
│   ├── LogBarangKeluar.php                ✏️ MODIFIED
│   ├── Permintaan.php                     ✏️ MODIFIED
│   ├── PermintaanItems.php                ✏️ MODIFIED
│   └── LogPermintaan.php                  ✏️ MODIFIED
└── Traits/
    └── FilterByPeriodeTahun.php           🆕 NEW
```

---

## 🚀 Next Steps

Setelah implementasi ini selesai:

1. **Change Periode Tahun tanpa Logout**
   - Buat fitur di Settings/Profile
   - User bisa ganti periode tahun kapan saja

2. **Audit Log**
   - Log setiap kali user mengubah periode tahun
   - Untuk tracking dan compliance

3. **Dashboard Widgets**
   - Tampilkan periode tahun yang sedang aktif
   - Quick switcher untuk ganti periode

4. **Export/Report**
   - Filter export data berdasarkan periode tahun
   - Atau provide option untuk lihat semua tahun

5. **Notification**
   - Notify user ketika periode tahun berubah
   - Reminder untuk memilih periode tahun jika belum

---

Jika ada pertanyaan atau klarifikasi, silakan hubungi tim development.
