document.addEventListener('DOMContentLoaded', function () {

  // ── Toggle password visibility ──
  document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', function () {
      const input = this.closest('.input-wrap').querySelector('input');
      const isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      this.textContent = isPass ? '🙈' : '👁️';
    });
  });

  // ── Register form validation ──
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
      let valid = true;

      const nama     = document.getElementById('nama');
      const email    = document.getElementById('email');
      const telp     = document.getElementById('telp');
      const pass     = document.getElementById('password');
      const konfirm  = document.getElementById('konfirmasi');
      const terms    = document.getElementById('terms');

      clearErrors();

      if (!nama.value.trim()) { showError('nama', 'Nama lengkap wajib diisi'); valid = false; }
      if (!validateEmail(email.value)) { showError('email', 'Format email tidak valid'); valid = false; }
      if (!validatePhone(telp.value)) { showError('telp', 'Nomor telepon tidak valid (min. 10 digit)'); valid = false; }
      if (pass.value.length < 8) { showError('password', 'Password minimal 8 karakter'); valid = false; }
      if (pass.value !== konfirm.value) { showError('konfirmasi', 'Konfirmasi password tidak cocok'); valid = false; }
      if (!terms.checked) {
        const alert = document.getElementById('alertBox');
        alert.textContent = 'Anda harus menyetujui syarat & ketentuan.';
        alert.className = 'alert alert-error show';
        valid = false;
      }

      if (!valid) e.preventDefault();
    });
  }

  // ── Login form validation ──
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      let valid = true;
      clearErrors();

      const email = document.getElementById('email');
      const pass  = document.getElementById('password');

      if (!validateEmail(email.value)) { showError('email', 'Format email tidak valid'); valid = false; }
      if (!pass.value.trim()) { showError('password', 'Password wajib diisi'); valid = false; }

      if (!valid) e.preventDefault();
    });
  }

  // ── Helpers ──
  function validateEmail(val) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val); }
  function validatePhone(val) { return /^[0-9]{10,15}$/.test(val.replace(/[\s\-]/g, '')); }

  function showError(fieldId, msg) {
    const field = document.getElementById(fieldId);
    const err   = document.getElementById(fieldId + 'Error');
    if (field) field.classList.add('error');
    if (err)   { err.textContent = msg; err.classList.add('show'); }
  }

  function clearErrors() {
    document.querySelectorAll('.form-group input').forEach(i => i.classList.remove('error'));
    document.querySelectorAll('.form-error').forEach(e => e.classList.remove('show'));
    const alert = document.getElementById('alertBox');
    if (alert) alert.classList.remove('show');
  }

  // ── Real-time password match indicator ──
  const konfirm = document.getElementById('konfirmasi');
  const pass    = document.getElementById('password');
  if (konfirm && pass) {
    konfirm.addEventListener('input', function () {
      const err = document.getElementById('konfirmasiError');
      if (this.value && this.value !== pass.value) {
        this.classList.add('error');
        if (err) { err.textContent = 'Password tidak cocok'; err.classList.add('show'); }
      } else {
        this.classList.remove('error');
        if (err) err.classList.remove('show');
      }
    });
  }

});