# SiMOK (Sistem Informasi Manajemen Organisasi Keanggotaan)

<p align="center">
    <strong>Sistem Manajemen Keanggotaan Modern berbasis Laravel 13</strong>
</p>

SiMOK adalah platform manajemen organisasi yang dirancang untuk mengelola data keanggotaan secara terpusat, efisien, dan modern. Aplikasi ini memfokuskan pada kemudahan pendaftaran, validasi data regional, dan pembuatan identitas digital bagi setiap anggota.

## ✨ Fitur Utama

- 📝 **Pendaftaran Mandiri & Manual**: Alur pendaftaran yang fleksibel bagi calon anggota maupun input langsung oleh admin.
- 🗺️ **Integrasi Data Regional**: Validasi wilayah berdasarkan data Provinsi, Kabupaten, Kecamatan, hingga Desa di Indonesia.
- 🆔 **Nomor Anggota Otomatis**: Generasi nomor anggota unik yang mengikuti standarisasi kode wilayah.
- 🪪 **Kartu Anggota Digital**: Pembuatan kartu anggota otomatis dengan QR Code yang dapat diunduh (PDF) atau dilihat secara langsung.
- 🔐 **Manajemen Hak Akses (RBAC)**: Pengaturan peran (Admin, Anggota) yang ketat menggunakan Spatie Laravel Permission.
- 📧 **Notifikasi Email**: Sistem pemberitahuan otomatis untuk status pendaftaran dan persetujuan anggota.
- 📊 **Dashboard Admin**: Ringkasan data statistik dan kontrol penuh atas manajemen data anggota.
- 🎨 **UI Modern & Responsif**: Dibangun dengan Tailwind CSS 4 untuk pengalaman pengguna yang premium di berbagai perangkat.

## 🚀 Teknologi Utama

- **Framework**: [Laravel 13](https://laravel.com)
- **Frontend**: [Livewire 4](https://livewire.laravel.com) & [Tailwind CSS 4](https://tailwindcss.com)
- **Database**: PostgreSQL / MySQL (Support JSON & Spatial data)
- **Reports**: [DOMPDF](https://github.com/barryvdh/laravel-dompdf) untuk PDF Generation
- **Security**: [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/introduction)
- **Testing**: [Pest PHP 4](https://pestphp.com)

## 🛠️ Instalasi

Pastikan Anda memiliki PHP 8.4+ dan Composer terinstal.

1. **Clone repository:**
   ```bash
   git clone https://github.com/syailendramudaberkarya/simok-webapp.git
   cd simok-webapp
   ```

2. **Setup environment:**
   ```bash
   composer run setup
   ```

3. **Inisialisasi Database:**
   ```bash
   php artisan migrate --seed
   ```

4. **Jalankan aplikasi:**
   ```bash
   composer run dev
   ```

## 📂 Struktur Proyek

- `app/Livewire`: Komponen reaktif untuk UI.
- `app/Models`: Definisi model data anggota dan regional.
- `database/migrations`: Struktur tabel database termasuk data wilayah.
- `resources/views/livewire`: Template Blade untuk antarmuka pengguna.
- `tests`: Suite pengujian menggunakan Pest.

## 📋 Lisensi

Proyek ini dilisensikan di bawah [MIT license](https://opensource.org/licenses/MIT).
