function togglePassword() {
    const form = document.querySelector('form');

    form.addEventListener('submit', function(event) {
        const nama = form.nama.value.trim();
        const email = form.email.value.trim();
        const telp = form.telp.value.trim();
        const password = form.password.value.trim();

        if (nama === '' || email === '' || telp === '' || password === '') {
            alert('Semua field harus diisi!');
            event.preventDefault();
            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert('Format email tidak valid!');
            event.preventDefault();
            return;
        }
        const telpPattern = /^\d+$/;
        if (!telpPattern.test(telp)) {
            alert('Nomor telepon harus berupa angka!');
            event.preventDefault();
            return;
        }

        if (password.length < 8) {
            alert('Password harus minimal 8 karakter!');
            event.preventDefault();
            return;
        }
    });
}

document.addEventListener('DOMContentLoaded', togglePassword);