-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Jun 2026 pada 20.24
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pesonantb2`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookmark`
--

CREATE TABLE `bookmark` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wisata_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bookmark`
--

INSERT INTO `bookmark` (`id`, `user_id`, `wisata_id`, `created_at`) VALUES
(1, 2, 3, '2026-06-12 01:36:21'),
(4, 8, 3, '2026-06-16 23:44:44'),
(5, 9, 8, '2026-06-17 18:03:02'),
(6, 10, 5, '2026-06-17 21:18:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `emoji` varchar(10) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `emoji`) VALUES
(1, 'Pantai', '🏖️'),
(2, 'Gunung', '🏔️'),
(3, 'Air Terjun', '💧'),
(4, 'Budaya', '🎭'),
(5, 'Pulau', '🏝️'),
(6, 'Kuliner', '🍜'),
(7, 'Adventure', '🧗');

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat`
--

CREATE TABLE `riwayat` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wisata_id` int(11) NOT NULL,
  `dilihat_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `riwayat`
--

INSERT INTO `riwayat` (`id`, `user_id`, `wisata_id`, `dilihat_at`) VALUES
(1, 1, 1, '2026-06-11 21:08:07'),
(17, 2, 6, '2026-06-12 00:30:13'),
(34, 2, 5, '2026-06-12 21:33:57'),
(45, 1, 2, '2026-06-13 00:00:28'),
(83, 2, 2, '2026-06-14 22:41:45'),
(95, 4, 1, '2026-06-15 00:27:11'),
(107, 1, 3, '2026-06-15 00:44:18'),
(108, 4, 3, '2026-06-15 00:44:39'),
(110, 8, 3, '2026-06-16 23:44:44'),
(111, 8, 5, '2026-06-16 23:49:58'),
(112, 8, 8, '2026-06-16 23:50:37'),
(117, 8, 1, '2026-06-16 23:51:45'),
(144, 2, 1, '2026-06-17 16:51:17'),
(151, 1, 12, '2026-06-17 18:13:34'),
(157, 10, 12, '2026-06-17 21:16:50'),
(158, 10, 9, '2026-06-17 21:17:15'),
(160, 10, 5, '2026-06-17 21:18:48'),
(163, 1, 6, '2026-06-17 21:26:51'),
(164, 10, 6, '2026-06-17 21:27:23'),
(165, 2, 7, '2026-06-17 21:31:45'),
(167, 2, 3, '2026-06-17 21:33:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wisata_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ulasan`
--

INSERT INTO `ulasan` (`id`, `user_id`, `wisata_id`, `parent_id`, `rating`, `komentar`, `created_at`) VALUES
(1, 2, 3, NULL, 5, NULL, '2026-06-12 00:01:27'),
(7, 2, 1, NULL, 5, 'baguss', '2026-06-15 00:25:31'),
(8, 4, 1, 7, NULL, 'benerr aku udah pernah kesana juga', '2026-06-15 00:26:52'),
(9, 4, 1, NULL, NULL, 'nyaman banget wisatanya', '2026-06-15 00:27:11'),
(13, 1, 3, 1, NULL, 'terima kasih', '2026-06-15 00:44:18'),
(14, 8, 1, 9, NULL, 'bagusss', '2026-06-16 23:51:40'),
(15, 8, 1, NULL, 5, NULL, '2026-06-16 23:51:45'),
(16, 9, 8, NULL, 3, 'tempetnya kumuh dan kotor, pemandunya ga ramah', '2026-06-17 18:03:27'),
(17, 1, 12, NULL, NULL, 'pengalaman nya buruk, kulkas nya harusnya dua pintu, gaada ac', '2026-06-17 18:13:34'),
(18, 10, 6, NULL, 5, 'bagus banget pemandangannya', '2026-06-17 21:15:21'),
(19, 1, 6, 18, NULL, 'terima kasih ulasannya kakak', '2026-06-17 21:26:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `foto_profil` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expired` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `telepon`, `password`, `role`, `foto_profil`, `created_at`, `reset_token`, `reset_expired`) VALUES
(1, 'Admin PesonaNTB', 'admin@pesonantb.com', '081234567890', '$2y$10$EUyVrBePDwzIE2l5YL4yzukW/OiHQxsWCUzr1u1pynadN.jT7G.N6', 'admin', 'profil_1_1781191270.jpeg', '2026-06-11 15:38:46', NULL, NULL),
(2, 'Rifdah Asyliyah', 'rifdah@gmail.com', '085337420926', '$2y$10$A4s8SRnXS/ENWO32n8Ogi.atqdZG19e8C7ytz3plL7zxpLEwJinAa', 'user', 'profil_2_1781191104.png', '2026-06-11 17:36:03', NULL, NULL),
(4, 'user1', 'user1@gmail.com', '081222333444', '$2y$10$PqbHp7jJQmjOyAtq4gHrZuGoCzAlXQaTft5ot2cBkfkzyLGiHNkj.', 'user', NULL, '2026-06-15 00:26:19', NULL, NULL),
(8, 'bujenk', 'bujenk@gmail.com', '088123456789', '$2y$10$1/OOKsiiOqxLN8khhjhaTuULHzliCOV8.uRLcjOYsdw5wLbhoI3P2', 'user', 'profil_8_1781624813.jpg', '2026-06-16 23:41:51', NULL, NULL),
(9, 'andreaSalma', 'andreaSalma@gmail.com', '08987654321', '$2y$10$xsg2TUk9qTnNpJdZ4a9sYewWorikkhC2q3DGOqPZ7PoU0xoL.nSlS', 'user', 'profil_9_1781690765.jpg', '2026-06-17 18:01:25', NULL, NULL),
(10, 'Bagus Imawan', 'bagus@gmail.com', '085111222333', '$2y$10$uTwpR7sIyxU8BJw7emvRBu12WEIxHkh4vCksv90bIUdubIKvvm2vG', 'user', 'profil_10_1781702298.png', '2026-06-17 21:13:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `wisata`
--

CREATE TABLE `wisata` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `kategori` enum('Pantai','Gunung','Air Terjun','Budaya','Pulau','Kuliner','Adventure') NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `fasilitas` text DEFAULT NULL,
  `jam_buka` varchar(100) DEFAULT NULL,
  `harga_tiket` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT 0.0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `wisata`
--

INSERT INTO `wisata` (`id`, `nama`, `kategori`, `lokasi`, `deskripsi`, `fasilitas`, `jam_buka`, `harga_tiket`, `foto`, `aktif`, `latitude`, `longitude`, `rating`, `created_at`, `updated_at`) VALUES
(1, 'Pantai Senggigi', 'Pantai', 'Lombok Barat', 'Pantai Senggigi adalah pantai ikonik di Lombok yang terkenal dengan pemandangan matahari terbenam yang memukau. Ombaknya yang tenang sangat cocok untuk berenang dan bersantai. Di sepanjang pantai terdapat berbagai restoran seafood dan penginapan dengan pemandangan langsung ke laut.', 'Parkir, Toilet, Mushola, Restoran, Penginapan, Penyewaan Payung', '08.00 - 18.00 WITA', 'Gratis', 'wisata_1781191720_6a2ad428c2bd0.jpg', 1, -8.49814353, 116.04528835, 4.8, '2026-06-11 15:38:46', '2026-06-12 23:59:48'),
(2, 'Gunung Rinjani', 'Gunung', 'Lombok Utara', 'Gunung Rinjani adalah gunung berapi tertinggi kedua di Indonesia dengan ketinggian 3.726 mdpl. Di puncaknya terdapat danau kawah Segara Anak yang menakjubkan. Pendakian Rinjani menjadi salah satu pengalaman petualangan paling populer di Indonesia.', 'Pos Pendakian, Pemandu Wisata, Camping Ground, Toilet', '24 Jam (Pendakian)', 'Rp 150.000/orang', 'wisata_1781180803_6a2aa983399a4.jpg', 1, -8.41210000, 116.46650000, 0.0, '2026-06-11 15:38:46', '2026-06-14 23:00:52'),
(3, 'Gili Trawangan', 'Pulau', 'Lombok Utara', 'Gili Trawangan adalah pulau kecil yang menjadi surga bawah laut di Lombok. Terkenal dengan snorkeling bersama penyu laut, menyelam, dan suasana pantai yang santai tanpa kendaraan bermotor. Kehidupan malam Gili Trawangan juga menjadi daya tarik tersendiri.', 'Penginapan, Restoran, Penyewaan Alat Snorkeling, Dive Center', '24 Jam', 'Tiket Kapal Rp 50.000', 'wisata_1781180833_6a2aa9a136534.jpg', 1, -8.35000000, 116.03000000, 5.0, '2026-06-11 15:38:46', '2026-06-17 21:28:58'),
(4, 'Pantai Pink', 'Pantai', 'Lombok Timur', 'Pantai Pink atau Pink Beach adalah salah satu dari hanya tujuh pantai berpasir merah muda di dunia. Warna merah muda pada pasirnya berasal dari pecahan terumbu karang merah yang bercampur dengan pasir putih. Terletak di kawasan Taman Nasional Gunung Rinjani.', 'Gazebo, Toilet, Snorkeling', '07.00 - 17.00 WITA', 'Rp 10.000/orang', 'wisata_1781180850_6a2aa9b2425fb.jpg', 1, -8.80190000, 116.52920000, 4.6, '2026-06-11 15:38:46', '2026-06-11 20:27:30'),
(5, 'Pulau Kenawa', 'Adventure', 'Poto Tano, Sumbawa Barat', 'Pulau Kenawa menawarkan pemandangan padang rumput luas yang memukau dengan kuda-kuda liar yang berkeliaran bebas. Sangat cocok untuk wisata alam, berkuda, dan menikmati keindahan alam Sumbawa yang masih sangat alami dan belum banyak terjamah.', 'Pemandu Wisata, Area Berkuda', '24 Jam', 'Rp 25.000/orang', 'wisata_1781180951_6a2aaa173830a.jpg', 1, -8.49811338, 116.83347144, 4.7, '2026-06-11 15:38:46', '2026-06-13 00:12:10'),
(6, 'Air Terjun Mata Jitu', 'Air Terjun', 'Unnamed Roa, Labuan Aji, Labuhan Badas, Sumbawa', 'Air Terjun Mata Jitu adalah pulau terpencil yang pernah dikunjungi oleh Putri Diana. Terkenal dengan air terjun tersembunyi yang indah, ekosistem bawah laut yang belum terjamah, dan ketenangan alam yang luar biasa. Menjadi destinasi wisata premium di Sumbawa.', 'Resort, Snorkeling, Trekking, Pemandu', '24 Jam', '', 'wisata_1781181259_6a2aab4bb1238.jpg', 1, -8.21156169, 117.52032048, 5.0, '2026-06-11 15:38:46', '2026-06-17 21:15:21'),
(7, 'Pantai Kuta Lombok', 'Pantai', 'Lombok Tengah', 'Pantai Kuta Lombok memiliki pasir putih halus seperti merica dengan ombak yang cocok untuk surfing. Berbeda dengan Kuta Bali, Kuta Lombok masih terasa lebih tenang dan alami. Panorama bukit di sekitar pantai menambah keindahan pemandangan.', 'Parkir, Toilet, Restoran, Penyewaan Papan Surfing', '06.00 - 18.00 WITA', 'Rp 5.000/orang', 'wisata_1781181316_6a2aab84cd4fc.jpg', 1, -8.89560000, 116.29170000, 4.7, '2026-06-11 15:38:46', '2026-06-11 20:35:16'),
(8, 'Air Terjun Sendang Gile', 'Air Terjun', 'Lombok Utara', 'Air Terjun Sendang Gile terletak di kaki Gunung Rinjani dengan ketinggian sekitar 30 meter. Airnya yang jernih dan segar berasal langsung dari mata air Gunung Rinjani. Menurut kepercayaan setempat, membasuh muka di air terjun ini dapat membuat awet muda.', 'Parkir, Toilet, Warung Makan, Pemandu', '07.00 - 17.00 WITA', 'Rp 15.000/orang', 'wisata_1781181284_6a2aab644007b.jpg', 1, -8.36000000, 116.40330000, 3.0, '2026-06-11 15:38:46', '2026-06-17 18:03:27'),
(9, 'Desa Sade', 'Budaya', 'Lombok Tengah', 'Desa Sade adalah desa adat Suku Sasak yang masih mempertahankan tradisi leluhur hingga saat ini. Rumah-rumah tradisional beratapkan ilalang dan berlantaikan campuran tanah dan kotoran kerbau masih terjaga kelestariannya. Pengunjung bisa menyaksikan langsung kehidupan adat Sasak.', 'Pemandu Wisata, Penjualan Kerajinan Tenun', '08.00 - 17.00 WITA', 'Rp 10.000/orang', 'wisata_1781181300_6a2aab7416618.jpg', 1, -8.85170000, 116.26670000, 4.5, '2026-06-11 15:38:46', '2026-06-11 20:35:00'),
(11, 'Pulau Satonda', 'Pulau', 'Bima', 'nhdcwasyhaoi', '', '', '', 'wisata_1781625265_6a3171b11504e.jpg', 1, 0.00000000, 0.00000000, 0.0, '2026-06-16 23:54:25', '2026-06-16 23:54:25'),
(12, 'kos kekalik', 'Budaya', 'mataram', 'tempat penampungan orang terlantar', '', '24 Jam', '1000000', 'wisata_1781691159_6a327317c0377.png', 1, -8.58821989, 116.08633771, NULL, '2026-06-17 18:12:39', '2026-06-17 18:13:34'),
(13, 'Sirkuit Mandalika', 'Adventure', 'Kuta Mandalika, Lombok Tengah', 'Sirkuit Internasional', '', '08.00 - 18.00 WITA', 'Rp 25.000/orang`', 'wisata_1781702642_6a329ff2acd28.jpg', 1, -8.89554555, 116.30369590, 0.0, '2026-06-17 21:23:49', '2026-06-17 21:24:02');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bookmark`
--
ALTER TABLE `bookmark`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bookmark` (`user_id`,`wisata_id`),
  ADD KEY `wisata_id` (`wisata_id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indeks untuk tabel `riwayat`
--
ALTER TABLE `riwayat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `wisata_id` (`wisata_id`);

--
-- Indeks untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wisata_id` (`wisata_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `telepon` (`telepon`);

--
-- Indeks untuk tabel `wisata`
--
ALTER TABLE `wisata`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bookmark`
--
ALTER TABLE `bookmark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `riwayat`
--
ALTER TABLE `riwayat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `wisata`
--
ALTER TABLE `wisata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookmark`
--
ALTER TABLE `bookmark`
  ADD CONSTRAINT `bookmark_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmark_ibfk_2` FOREIGN KEY (`wisata_id`) REFERENCES `wisata` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `riwayat`
--
ALTER TABLE `riwayat`
  ADD CONSTRAINT `riwayat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_ibfk_2` FOREIGN KEY (`wisata_id`) REFERENCES `wisata` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ulasan_ibfk_2` FOREIGN KEY (`wisata_id`) REFERENCES `wisata` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
