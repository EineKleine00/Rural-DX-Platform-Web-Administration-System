# Rural DX Platform - Web Administration System

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

**Rural DX Platform** adalah Sistem Informasi Administrasi Desa berbasis web yang dirancang untuk mendigitalisasi pelayanan publik. Sistem ini mempercepat proses administrasi melalui pengelolaan data warga terpadu dan otomatisasi pembuatan surat keterangan.

## 🚀 Fitur Utama

### 1. 👥 Manajemen Kependudukan (Data Warga)
Pengelolaan database penduduk yang lengkap dan terpusat:
* **Data Detail:** Mencatat NIK, No. KK, Nama, Tempat/Tanggal Lahir, hingga Pekerjaan.
* **Pencarian Cepat:** Memudahkan petugas mencari data warga untuk keperluan administrasi.

### 2. 📄 Otomatisasi Surat (Smart Templates)
Fitur generate surat otomatis yang menghemat waktu:
* **Master Template:** Mendukung berbagai jenis surat (Surat Domisili, SKTM, Surat Kematian, dll).
* **Auto-Fill:** Data warga otomatis terisi ke dalam draf surat hanya dengan memilih NIK.
* **Arsip Surat:** Riwayat surat keluar tersimpan rapi dengan penomoran otomatis.

### 3. 🔐 Multi-User Authentication
Sistem keamanan berbasis peran (Role-Based Access Control):
* **Admin:** Mengelola pengaturan sistem, manajemen user, dan data master.
* **Petugas:** Fokus pada pelayanan loket, input data warga, dan pencetakan surat.

## 🛠️ Teknologi

* **Backend:** Laravel 12 & Laravel Breeze
* **Database:** MySQL
* **Frontend:** Blade Templates & Tailwind CSS
* **Server:** Apache/Nginx

## 📦 Instalasi & Penggunaan

1.  **Clone Repository**
    ```bash
    git clone [https://github.com/EineKleine00/Rural-DX-Platform-Web-Administration-System.git](https://github.com/EineKleine00/Rural-DX-Platform-Web-Administration-System.git)
    cd Rural-DX-Platform-Web-Administration-System
    ```

2.  **Setup Environment**
    ```bash
    composer install
    npm install
    cp .env.example .env
    php artisan key:generate
    ```

3.  **Konfigurasi Database**
    Sesuaikan file `.env` dengan database lokal Anda, lalu jalankan:
    ```bash
    php artisan migrate:fresh --seed
    ```
    *(Otomatis membuat akun Admin & data dummy)*

4.  **Jalankan Aplikasi**
    ```bash
    npm run dev
    php artisan serve
    ```

## 🔐 Akun Demo

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@kelurahan.id` | `password` |
| **Petugas** | `petugas@example.com` | `password` |

---
*Dibuat untuk mempermudah digitalisasi administrasi desa.*
