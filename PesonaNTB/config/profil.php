<?php 
session_start(); 
require_once 'config/db.php'; 

if (!isset($_SESSION['user_id'])) { 
    header('Location: config/login.php'); 
    exit; 
} 

$uid = $_SESSION['user_id']; 

// Ambil data user 
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?"); 
$stmt->bind_param('i', $uid); 
$stmt->execute(); 
$user = $stmt->get_result()->fetch_assoc(); 
$stmt->close(); 

$success = ''; 
$error = ''; 

// Handle update profil 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) { 
    
    // ==========================================
    // PERBAIKAN 1: Proteksi Backend untuk Admin
    // ==========================================
    if ($_POST['action'] === 'hapus_akun') { 
        if ($_SESSION['role'] === 'admin') {
            $error = 'Akun Administrator tidak dapat dihapus melalui halaman profil!';
        } else {
            $konfirm_hapus = trim($_POST['konfirm_hapus'] ?? ''); 
            // Verifikasi password sebelum hapus 
            if (!password_verify($konfirm_hapus, $user['password'])) { 
                $error = 'Password salah. Akun tidak dihapus.'; 
            } else { 
                // Hapus semua data terkait user 
                $conn->query("DELETE FROM bookmark WHERE user_id=$uid"); 
                $conn->query("DELETE FROM ulasan WHERE user_id=$uid"); 
                $conn->query("DELETE FROM riwayat WHERE user_id=$uid"); 
                $conn->query("DELETE FROM users WHERE id=$uid"); 
                // Logout 
                session_unset(); 
                session_destroy(); 
                header('Location: register.php?msg=Akun+berhasil+dihapus.'); 
                exit; 
            } 
        }
    } 

    if ($_POST['action'] === 'upload_foto') { 
        if (!empty($_FILES['foto_profil']['name'])) { 
            $ext_allowed = ['jpg','jpeg','png','webp']; 
            $ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION)); 
            if (!in_array($ext, $ext_allowed)) { 
                $error = 'Format foto tidak valid. Gunakan JPG, PNG, atau WebP.'; 
            } elseif ($_FILES['foto_profil']['size'] > 2 * 1024 * 1024) { 
                $error = 'Ukuran foto maksimal 2MB.'; 
            } else { 
                $new_name = 'profil_' . $uid . '_' . time() . '.' . $ext; 
                $base_path   = dirname(__FILE__) . '/uploads/profil/'; 
                // Buat folder jika belum ada 
                if (!is_dir($base_path)) mkdir($base_path, 0755, true); 
                $upload_path = $base_path . $new_name; 
                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_path)) { 
                    // Hapus foto lama 
                    if (!empty($user['foto_profil']) && file_exists($base_path . $user['foto_profil'])) { 
                        unlink($base_path . $user['foto_profil']); 
                    } 
                    $stmt = $conn->prepare("UPDATE users SET foto_profil=? WHERE id=?"); 
                    $stmt->bind_param('si', $new_name, $uid); 
                    $stmt->execute(); 
                    $stmt->close(); 
                    $user['foto_profil'] = $new_name; 
                    $success = 'Foto profil berhasil diperbarui.'; 
                } else { 
                    $error = 'Gagal mengupload foto. Coba lagi.'; 
                } 
            } 
        } else { 
            $error = 'Pilih foto terlebih dahulu.'; 
        } 
    } 

    if ($_POST['action'] === 'update_profil') { 
        $nama  = trim($_POST['nama']  ?? ''); 
        $telp  = trim($_POST['telp']  ?? ''); 
        $email = trim($_POST['email'] ?? ''); 
        if (empty($nama)) { 
            $error = 'Nama tidak boleh kosong.'; 
        } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            $error = 'Format email tidak valid.'; 
        } else { 
            // Cek apakah email sudah dipakai orang lain 
            $stmt = $conn->prepare("SELECT id FROM users WHERE email=? AND id!=?"); 
            $stmt->bind_param('si', $email, $uid); 
            $stmt->execute(); 
            $stmt->store_result(); 
            if ($stmt->num_rows > 0) { 
                $error = 'Email sudah digunakan akun lain. Gunakan email berbeda.'; 
                $stmt->close(); 
            } else { 
                $stmt->close(); 
                $stmt = $conn->prepare("UPDATE users SET nama=?, telepon=?, email=? WHERE id=?"); 
                $stmt->bind_param('sssi', $nama, $telp, $email, $uid); 
                $stmt->execute(); 
                $stmt->close(); 
                $_SESSION['user_nama'] = $nama; 
                $user['nama']      = $nama; 
                $user['telepon']  = $telp; 
                $user['email']    = $email; 
                $success = 'Profil berhasil diperbarui.'; 
            } 
        } 
    } 

    if ($_POST['action'] === 'update_password') { 
        $old  = $_POST['old_password']  ?? ''; 
        $new  = $_POST['new_password']  ?? ''; 
        $conf = $_POST['conf_password'] ?? ''; 
        if (!password_verify($old, $user['password'])) { 
            $error = 'Password lama tidak sesuai.'; 
        } elseif (strlen($new) < 8) { 
            $error = 'Password baru minimal 8 karakter.'; 
        } elseif ($new !== $conf) { 
            $error = 'Konfirmasi password tidak cocok.'; 
        } else { 
            $hash = password_hash($new, PASSWORD_BCRYPT); 
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?"); 
            $stmt->bind_param('si', $hash, $uid); 
            $stmt->execute(); 
            $stmt->close(); 
            $success = 'Password berhasil diubah.'; 
        } 
    } 
} 

// Hitung stats 
$jml_bookmark = $conn->query("SELECT COUNT(*) as c FROM bookmark WHERE user_id=$uid")->fetch_assoc()['c'] ?? 0; 
$jml_ulasan   = $conn->query("SELECT COUNT(*) as c FROM ulasan WHERE user_id=$uid")->fetch_assoc()['c'] ?? 0; 
$jml_riwayat  = $conn->query("SELECT COUNT(*) as c FROM riwayat WHERE user_id=$uid")->fetch_assoc()['c'] ?? 0; 
$inisial = strtoupper(mb_substr($user['nama'], 0, 2)); 
?> 
<!DOCTYPE html> 
<html lang="id"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Profil &mdash; PesonaNTB</title> 
    <link rel="stylesheet" href="css/style.css"> 
    <link rel="stylesheet" href="css/user.css"> 
</head> 
<body> 
<?php include 'config/navbar.php'; ?> 

<div class="user-page"> 
    <div class="user-container"> 
        <div class="section-header"> 
            <span class="section-label">✦ Akun Saya</span> 
            <h1 class="section-title">Profil Pengguna</h1> 
        </div> 
        
        <div class="profil-grid"> 
            <div class="profil-sidebar"> 
                <?php if (!empty($user['foto_profil']) && file_exists(dirname(__FILE__) . '/assets/uploads/profil/' . $user['foto_profil'])): ?> 
                    <div class="profil-avatar" style="background:none;overflow:hidden;padding:0"> 
                        <img src="assets/uploads/profil/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Foto Profil" style="width:100%;height:100%;object-fit:cover;border-radius:50%"> 
                    </div> 
                <?php else: ?> 
                    <div class="profil-avatar"><?= $inisial ?></div> 
                <?php endif; ?> 
                
                <div class="profil-nama"><?= htmlspecialchars($user['nama']) ?></div> 
                
                <form method="POST" enctype="multipart/form-data" style="margin:0.75rem 0"> 
                    <input type="hidden" name="action" value="upload_foto"> 
                    <label for="fotoProfilInput" style="display:inline-block;padding:0.4rem 1rem;border-radius:20px;border:1.5px solid var(--sand);background:var(--cream);color:var(--brown);font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.2s"> 
                        📷 Ganti Foto 
                    </label> 
                    <input type="file" id="fotoProfilInput" name="foto_profil" accept="image/jpg,image/jpeg,image/png,image/webp" style="display:none" onchange="this.form.submit()"> 
                </form> 
                
                <div style="font-size:0.72rem;color:var(--text-muted);margin-top:-0.5rem;margin-bottom:0.5rem">JPG, PNG, WebP · Maks 2MB</div> 
                <div class="profil-email"><?= htmlspecialchars($user['email']) ?></div> 
                
                <div class="profil-stats"> 
                  <?php if ($_SESSION['role'] !== 'admin'): ?>
                    <div><div class="pstat-num"><?= $jml_bookmark ?></div><div class="pstat-label">Tersimpan</div></div> 
                  <?php endif; ?>

                  <div><div class="pstat-num"><?= $jml_ulasan ?></div><div class="pstat-label">Ulasan</div></div> 
    
                  <?php if ($_SESSION['role'] !== 'admin'): ?>
                    <div><div class="pstat-num"><?= $jml_riwayat ?></div><div class="pstat-label">Dilihat</div></div> 
                  <?php endif; ?>
                </div> 
                
                <nav class="profil-nav"> 
                  <a href="profil.php" class="active">👤 Profil Saya</a> 
                  <?php if ($_SESSION['role'] !== 'admin'): ?>
                    <a href="bookmark.php">🔖 Tersimpan</a> 
                    <a href="riwayat.php">🕐 Riwayat</a> 
                  <?php endif; ?>
                  <a href="logout.php" style="color:#C0392B">🚪 Keluar</a> 
                </nav> 
            </div> 
            
            <div class="profil-main"> 
                <?php if ($success): ?> 
                    <div class="alert alert-success show"><?= htmlspecialchars($success) ?></div> 
                <?php elseif ($error): ?> 
                    <div class="alert alert-error show"><?= htmlspecialchars($error) ?></div> 
                <?php endif; ?> 
                
                <div class="profil-card"> 
                    <h3>Edit Informasi Profil</h3> 
                    <form method="POST"> 
                        <input type="hidden" name="action" value="update_profil"> 
                        <div class="profil-form-row"> 
                            <div class="form-group-profil"> 
                                <label>Nama Lengkap</label> 
                                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required> 
                            </div> 
                            <div class="form-group-profil"> 
                                <label>Nomor Telepon</label> 
                                <input type="tel" name="telp" value="<?= htmlspecialchars($user['telepon'] ?? '') ?>"> 
                            </div> 
                        </div> 
                        <div class="form-group-profil"> 
                            <label>Email</label> 
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Masukkan email baru"> 
                            <span style="font-size:0.75rem;color:var(--text-muted)">Email digunakan untuk login. Pastikan email aktif.</span> 
                        </div> 
                        <div class="form-group-profil"> 
                            <label>Role</label> 
                            <input type="text" value="<?= ucfirst($user['role']) ?>" readonly> 
                        </div> 
                        <div class="form-group-profil"> 
                            <label>Bergabung Sejak</label> 
                            <input type="text" value="<?= date('d F Y', strtotime($user['created_at'])) ?>" readonly> 
                        </div> 
                        <button type="submit" class="btn-save">Simpan Perubahan</button> 
                    </form> 
                </div> 
                
                <div class="profil-card"> 
                    <h3>Ubah Password</h3> 
                    <form method="POST"> 
                        <input type="hidden" name="action" value="update_password"> 
                        <div class="form-group-profil"> 
                            <label>Password Lama</label> 
                            <input type="password" name="old_password" placeholder="Masukkan password lama"> 
                        </div> 
                        <div class="profil-form-row"> 
                            <div class="form-group-profil"> 
                                <label>Password Baru</label> 
                                <input type="password" name="new_password" placeholder="Min. 8 karakter"> 
                            </div> 
                            <div class="form-group-profil"> 
                                <label>Konfirmasi Password Baru</label> 
                                <input type="password" name="conf_password" placeholder="Ulangi password baru"> 
                            </div> 
                        </div> 
                        <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:0.25rem"> 
                            <button type="submit" class="btn-save">Ubah Password</button> 
                        </div> 
                    </form> 
                </div> 
                
                <?php if ($_SESSION['role'] !== 'admin'): ?> 
                <div class="profil-card"> 
                    <h3>Zona Berbahaya</h3> 
                    <p style="font-size:0.88rem;color:var(--text-muted);margin-bottom:1.25rem">Menghapus akun bersifat <strong>permanen</strong> dan tidak dapat dibatalkan. Semua data termasuk bookmark dan ulasan akan ikut terhapus.</p> 
                    
                    <?php if (!isset($_GET['confirm_hapus'])): ?> 
                        <a href="profil.php?confirm_hapus=1" class="btn-danger">Hapus Akun Saya</a> 
                    <?php else: ?> 
                        <div style="background:#FDEEEE;border:1px solid #F5C6C6;border-radius:10px;padding:1.25rem;margin-bottom:1rem"> 
                            <p style="font-size:0.85rem;color:#C0392B;font-weight:600;margin-bottom:0.75rem">⚠️ Konfirmasi Penghapusan Akun</p> 
                            <p style="font-size:0.83rem;color:#C0392B;margin-bottom:1rem">Masukkan password kamu untuk mengkonfirmasi penghapusan akun.</p> 
                            <form method="POST"> 
                                <input type="hidden" name="action" value="hapus_akun"> 
                                <div class="form-group-profil" style="margin-bottom:1rem"> 
                                    <label style="color:#C0392B">Password</label> 
                                    <input type="password" name="konfirm_hapus" placeholder="Masukkan password kamu" required> 
                                </div> 
                                <div style="display:flex;gap:0.75rem"> 
                                    <button type="submit" class="btn-danger">Ya, Hapus Akun Saya</button> 
                                    <a href="profil.php" class="btn-save" style="text-decoration:none">Batal</a> 
                                </div> 
                            </form> 
                        </div> 
                    <?php endif; ?> 
                </div> 
                <?php endif; ?> 
                
            </div> 
        </div> 
    </div> 
</div> 

<?php include 'config/footer.php'; ?> 
<script src="js/main.js"></script> 
</body> 
</html>