-- ============================================
-- DATABASE: PesonaNTB
-- Sistem Informasi Wisata Nusa Tenggara Barat
-- ============================================

CREATE DATABASE IF NOT EXISTS pesonantb2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pesonantb2;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    telepon     VARCHAR(20)   NOT NULL,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('user','admin') DEFAULT 'user',
    foto_profil VARCHAR(255) DEFAULT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel wisata
CREATE TABLE IF NOT EXISTS wisata (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(150)  NOT NULL,
    kategori    ENUM('Pantai','Gunung','Air Terjun','Budaya','Pulau','Kuliner','Adventure') NOT NULL,
    lokasi      VARCHAR(150)  NOT NULL,
    deskripsi   TEXT          NOT NULL,
    fasilitas   TEXT,
    jam_buka    VARCHAR(100),
    harga_tiket VARCHAR(100),
    foto        VARCHAR(255),
    aktif       TINYINT(1) DEFAULT 1,
    latitude    DECIMAL(10,8),
    longitude   DECIMAL(11,8),
    rating      DECIMAL(3,1)  DEFAULT 0.0,
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel kategori
CREATE TABLE IF NOT EXISTS kategori (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    nama  VARCHAR(100) NOT NULL UNIQUE,
    emoji VARCHAR(10)  DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data awal kategori
INSERT INTO kategori (nama, emoji) VALUES
('Pantai', '🏖️'), ('Gunung', '🏔️'), ('Air Terjun', '💧'),
('Budaya', '🎭'), ('Pulau', '🏝️'), ('Kuliner', '🍜'), ('Adventure', '🧗');

-- Tabel bookmark
CREATE TABLE IF NOT EXISTS bookmark (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    wisata_id  INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bookmark (user_id, wisata_id),
    FOREIGN KEY (user_id)   REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (wisata_id) REFERENCES wisata(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel ulasan
CREATE TABLE IF NOT EXISTS ulasan (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    wisata_id  INT NOT NULL,
    rating     TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    komentar   TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_ulasan (user_id, wisata_id),
    FOREIGN KEY (user_id)   REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (wisata_id) REFERENCES wisata(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel riwayat
CREATE TABLE IF NOT EXISTS riwayat (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    wisata_id  INT NOT NULL,
    dilihat_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)   REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (wisata_id) REFERENCES wisata(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- DATA AWAL: Admin
-- Password: admin123
-- ============================================
INSERT INTO users (nama, email, telepon, password, role) VALUES
('Admin PesonaNTB', 'admin@pesonantb.com', '081234567890',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ============================================
-- DATA AWAL: Destinasi Wisata
-- ============================================
INSERT INTO wisata (nama, kategori, lokasi, deskripsi, fasilitas, jam_buka, harga_tiket, rating, latitude, longitude) VALUES
('Pantai Senggigi',
 'Pantai', 'Lombok Barat',
 'Pantai Senggigi adalah pantai ikonik di Lombok yang terkenal dengan pemandangan matahari terbenam yang memukau. Ombaknya yang tenang sangat cocok untuk berenang dan bersantai. Di sepanjang pantai terdapat berbagai restoran seafood dan penginapan dengan pemandangan langsung ke laut.',
 'Parkir, Toilet, Mushola, Restoran, Penginapan, Penyewaan Payung',
 '08.00 - 18.00 WITA', 'Gratis', 4.8, -8.4955, 116.0522),

('Gunung Rinjani',
 'Gunung', 'Lombok Utara',
 'Gunung Rinjani adalah gunung berapi tertinggi kedua di Indonesia dengan ketinggian 3.726 mdpl. Di puncaknya terdapat danau kawah Segara Anak yang menakjubkan. Pendakian Rinjani menjadi salah satu pengalaman petualangan paling populer di Indonesia.',
 'Pos Pendakian, Pemandu Wisata, Camping Ground, Toilet',
 '24 Jam (Pendakian)', 'Rp 150.000/orang', 4.9, -8.4121, 116.4665),

('Gili Trawangan',
 'Pulau', 'Lombok Utara',
 'Gili Trawangan adalah pulau kecil yang menjadi surga bawah laut di Lombok. Terkenal dengan snorkeling bersama penyu laut, menyelam, dan suasana pantai yang santai tanpa kendaraan bermotor. Kehidupan malam Gili Trawangan juga menjadi daya tarik tersendiri.',
 'Penginapan, Restoran, Penyewaan Alat Snorkeling, Dive Center',
 '24 Jam', 'Tiket Kapal Rp 50.000', 4.7, -8.3500, 116.0300),

('Pantai Pink',
 'Pantai', 'Lombok Timur',
 'Pantai Pink atau Pink Beach adalah salah satu dari hanya tujuh pantai berpasir merah muda di dunia. Warna merah muda pada pasirnya berasal dari pecahan terumbu karang merah yang bercampur dengan pasir putih. Terletak di kawasan Taman Nasional Gunung Rinjani.',
 'Gazebo, Toilet, Snorkeling', '07.00 - 17.00 WITA', 'Rp 10.000/orang', 4.6, -8.8019, 116.5292),

('Savana Sumbawa',
 'Adventure', 'Sumbawa Besar',
 'Savana Sumbawa menawarkan pemandangan padang rumput luas yang memukau dengan kuda-kuda liar yang berkeliaran bebas. Sangat cocok untuk wisata alam, berkuda, dan menikmati keindahan alam Sumbawa yang masih sangat alami dan belum banyak terjamah.',
 'Pemandu Wisata, Area Berkuda', '06.00 - 17.00 WITA', 'Rp 25.000/orang', 4.7, -8.4892, 117.4167),

('Pulau Moyo',
 'Pulau', 'Sumbawa',
 'Pulau Moyo adalah pulau terpencil yang pernah dikunjungi oleh Putri Diana. Terkenal dengan air terjun tersembunyi yang indah, ekosistem bawah laut yang belum terjamah, dan ketenangan alam yang luar biasa. Menjadi destinasi wisata premium di Sumbawa.',
 'Resort, Snorkeling, Trekking, Pemandu', '24 Jam', 'Rp 20.000/orang', 4.8, -8.2667, 117.6167),

('Pantai Kuta Lombok',
 'Pantai', 'Lombok Tengah',
 'Pantai Kuta Lombok memiliki pasir putih halus seperti merica dengan ombak yang cocok untuk surfing. Berbeda dengan Kuta Bali, Kuta Lombok masih terasa lebih tenang dan alami. Panorama bukit di sekitar pantai menambah keindahan pemandangan.',
 'Parkir, Toilet, Restoran, Penyewaan Papan Surfing', '06.00 - 18.00 WITA', 'Rp 5.000/orang', 4.7, -8.8956, 116.2917),

('Air Terjun Sendang Gile',
 'Air Terjun', 'Lombok Utara',
 'Air Terjun Sendang Gile terletak di kaki Gunung Rinjani dengan ketinggian sekitar 30 meter. Airnya yang jernih dan segar berasal langsung dari mata air Gunung Rinjani. Menurut kepercayaan setempat, membasuh muka di air terjun ini dapat membuat awet muda.',
 'Parkir, Toilet, Warung Makan, Pemandu', '07.00 - 17.00 WITA', 'Rp 15.000/orang', 4.6, -8.3600, 116.4033),

('Desa Sade',
 'Budaya', 'Lombok Tengah',
 'Desa Sade adalah desa adat Suku Sasak yang masih mempertahankan tradisi leluhur hingga saat ini. Rumah-rumah tradisional beratapkan ilalang dan berlantaikan campuran tanah dan kotoran kerbau masih terjaga kelestariannya. Pengunjung bisa menyaksikan langsung kehidupan adat Sasak.',
 'Pemandu Wisata, Penjualan Kerajinan Tenun', '08.00 - 17.00 WITA', 'Rp 10.000/orang', 4.5, -8.8517, 116.2667);
