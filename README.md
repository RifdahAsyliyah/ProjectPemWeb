# 🌴 PesonaNTB - Sistem Informasi Destinasi Wisata Nusa Tenggara Barat

## 📖 Deskripsi

PesonaNTB adalah sistem informasi pariwisata berbasis web yang dirancang untuk membantu wisatawan menemukan dan memperoleh informasi lengkap mengenai berbagai destinasi wisata di Nusa Tenggara Barat (NTB), khususnya Pulau Lombok dan Pulau Sumbawa.

Website ini menyediakan informasi destinasi wisata, lokasi, kategori wisata, fasilitas, harga tiket, rating, ulasan, serta fitur personalisasi seperti bookmark, riwayat kunjungan, dan dashboard pengguna. Selain itu, tersedia panel admin untuk mengelola seluruh data wisata dan pengguna.

# 🎯 Tujuan

* Memperkenalkan destinasi wisata Nusa Tenggara Barat kepada masyarakat luas.
* Menyediakan informasi wisata yang lengkap dan mudah diakses.
* Membantu wisatawan menemukan destinasi wisata sesuai kebutuhan.
* Mendukung promosi pariwisata daerah melalui platform digital.

# 👨‍💻 Tim Pengembang

### Rifdah Asyliyah

**Backend Developer**

Tanggung Jawab:

* Perancangan database MySQL
* Pengembangan sistem menggunakan PHP Native
* Implementasi autentikasi pengguna
* Pengelolaan session
* Pengembangan fitur bookmark
* Pengembangan fitur riwayat kunjungan
* Pengembangan dashboard user
* Pengembangan dashboard admin
* Implementasi sistem ulasan
* Implementasi forgot password & reset password

### Melani Putri Zahari

**Frontend Developer**

Tanggung Jawab:

* Desain antarmuka website
* Implementasi HTML, CSS, dan JavaScript
* Pengembangan tampilan responsif
* Pengembangan UI/UX website

# 👥 Aktor Sistem

## 1. Guest (Pengunjung)

Dapat melakukan:

* Melihat halaman beranda
* Melihat daftar destinasi wisata
* Melihat kategori wisata
* Melihat detail destinasi
* Registrasi akun
* Login akun

## 2. User

Dapat melakukan:

* Login dan Logout
* Mengelola profil
* Upload foto profil
* Mengubah data profil
* Melihat dashboard pengguna
* Melihat seluruh destinasi wisata
* Mencari destinasi wisata
* Filter destinasi berdasarkan kategori
* Melihat detail destinasi
* Menyimpan destinasi ke bookmark
* Menghapus bookmark
* Melihat riwayat kunjungan
* Menghapus riwayat kunjungan
* Memberikan ulasan dan rating
* Mengakses lokasi melalui Google Maps
* Menggunakan fitur lupa password

## 3. Admin

Dapat melakukan:

* Login Admin
* Mengakses dashboard admin
* Mengelola data wisata
* Menambah destinasi wisata
* Mengubah destinasi wisata
* Menghapus destinasi wisata
* Mengelola data pengguna
* Mengelola ulasan pengguna
* Melihat statistik website

# ✨ Fitur Utama

## 🔐 Autentikasi

* Login
* Register
* Logout
* Forgot Password
* Reset Password

## 👤 Profil Pengguna

* Edit profil
* Upload foto profil
* Statistik aktivitas pengguna

## 🏖️ Destinasi Wisata

* Daftar destinasi
* Detail destinasi
* Pencarian destinasi
* Filter kategori
* Informasi wisata lengkap
* Integrasi Google Maps

## ⭐ Ulasan

* Tambah ulasan
* Rating destinasi
* Menampilkan ulasan pengguna

## 🔖 Bookmark

* Simpan destinasi favorit
* Hapus bookmark
* Statistik bookmark

## 🕒 Riwayat Kunjungan

* Riwayat otomatis saat melihat destinasi
* Hapus riwayat tertentu
* Hapus seluruh riwayat

## 📊 Dashboard User

* Statistik bookmark
* Statistik riwayat
* Statistik ulasan
* Aktivitas terbaru
* Destinasi favorit

## 🛠️ Dashboard Admin

* Total destinasi wisata
* Total pengguna
* Total ulasan
* Kelola wisata
* Kelola pengguna
* Kelola ulasan

# 🗺️ Sitemap

## Guest

* Home
* Destinasi
* Kategori
* Detail Destinasi
* Login
* Register
* Forgot Password

## User

* Dashboard Saya
* Profil Saya
* Destinasi
* Detail Destinasi
* Bookmark
* Riwayat
* Logout

## Admin

* Dashboard Admin
* Kelola Wisata
* Tambah Wisata
* Edit Wisata
* Hapus Wisata
* Kelola Pengguna
* Kelola Ulasan
* Logout

# 📁 Struktur Folder

```text
PesonaNTB
│
├── index.php
│
├── config/
│   ├── ajax_toggle.php
│   ├── auth_guard.php
│   ├── bookmark.php
│   ├── dashboard.php
│   ├── db.php
│   ├── destinasi.php
│   ├── detail.php
│   ├── footer.php
│   ├── forgot_password.php
│   ├── kategori.php
│   ├── login.php
│   ├── logout.php
│   ├── navbar.php
│   ├── pengguna.php
│   ├── profil.php
│   ├── register.php
│   ├── reset_password.php
│   ├── riwayat.php
│   ├── sidebar.php
│   ├── topbar.php
│   ├── ulasan.php
│   ├── user_dashboard.php
│   ├── wisata.php
│   └── wisata_form.php
│
├── assets/
│   └── uploads/
│       ├── destinasi/
│       └── profil/
│
├── css/
│   ├── admin.css
│   ├── auth.css
│   ├── destinasi.css
│   ├── style.css
│   └── user.css
│
├── js/
│   ├── admin.js
│   ├── auth.js
│   └── main.js
│
└── database/
    └── pesonantb.sql
```

# 💻 Tech Stack

## Frontend

* HTML5
* CSS3
* JavaScript

## Backend

* PHP Native

## Database

* MySQL

## Development Tools

* XAMPP
* phpMyAdmin
* Visual Studio Code
* Google Chrome


