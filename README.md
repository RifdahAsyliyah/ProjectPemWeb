# Pesona NTB: Sistem Informasi Rekomendasi Destinasi Wisata NTB
Temukan Pesona Tersembunyi dari Nusa Tenggara Barat.
## Deskripsi Singkat
PesonaNTB adalah website sistem informasi pariwisata berbasis web yang dibuat untuk memperkenalkan berbagai destinasi wisata di Nusa Tenggara Barat, khususnya di Pulau Lombok dan Pulau Sumbawa.

Website ini dirancang sebagai media informasi yang membantu wisatawan memperoleh referensi tempat wisata secara mudah, cepat, dan menarik. PesonaNTB juga menjadi sarana untuk mendukung promosi pariwisata daerah agar keindahan alam dan budaya Nusa Tenggara Barat semakin dikenal oleh masyarakat luas.
## Team Roles
1. Rifdah Asyliyah (Backend Developer): Mengembangkan logika sistem menggunakan PHP, mengelola autentikasi, session, serta merancang dan menghubungkan database MySQL.
2. Melani Putri Zahari (Frontend Developer): Mendesain tampilan website menggunakan HTML, CSS, dan JavaScript serta menata layout halaman agar responsif dan menarik.
## Menu Utama
- Pengunjung (Belum Login)
  - Landing Page
  - About
  - Destinations (Wisata Populer)
  - Sign In
  - Sign Up

- User (Setelah Login)
  - Landing Page
  - About
  - Explore NTB (Semua Destinasi)
  - Search Wisata
  - Detail Wisata
  - Lihat Lokasi di Google Maps
  - Profile Page
  - Logout

- Admin
  - Dashboard Admin
  - Tambah Wisata
  - Edit Wisata
  - Hapus Wisata
  - Logout
## Sitemap
PesonaNTB/
├── index.html
├── logout.php
├── pesonantb.sql
├── Dokumentasi_PesonaNTB.pdf
│
├── assets/
│   ├── foto/
│   │   └── (seluruh gambar destinasi wisata)
│   └── uploads/
│       └── (gambar hasil upload admin)
│
├── config/
│   ├── koneksi.php
│   ├── login.php
│   ├── signup.php
│   └── hapus_wisata.php
│
├── html/
│   ├── admin.php
│   ├── signin.html
│   ├── signup.html
│   ├── forgot-password.html
│   ├── tambah_wisata.php
│   └── edit_wisata.php
│
├── script/
│   └── scriptSignUp.js
│
└── style/
    ├── styleLandingPage.css
    ├── styleAuth.css
    ├── styleAdmin.css
    ├── styleLogin.css
    ├── styleSignup.css
    ├── styleTambahWisata.css
    └── styleEditWisata.css

## Aktor
1. Pengunjung (Guest)
2. User
3. Admin
## Tech Stack
### Frontend
- HTML5
- CSS3
- JavaScript
### Backend
- PHP Native
### Database
- MySQL
### Development Tools
- XAMPP
- phpMyAdmin
- Visual Studio Code
- Google Chrome

