document.addEventListener('DOMContentLoaded', function () {

  // ── Sidebar toggle (mobile) ──
  const sidebar = document.getElementById('adminSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const closeBtn  = document.getElementById('sidebarClose');

  if (toggleBtn) toggleBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
  if (closeBtn)  closeBtn.addEventListener('click',  () => sidebar.classList.remove('open'));
  document.addEventListener('click', (e) => {
    if (sidebar && !sidebar.contains(e.target) && toggleBtn && !toggleBtn.contains(e.target)) {
      sidebar.classList.remove('open');
    }
  });

  // ── Confirm delete modal ──
  const modal       = document.getElementById('confirmModal');
  const modalMsg    = document.getElementById('modalMsg');
  const modalConfirm= document.getElementById('modalConfirm');
  const modalCancel = document.getElementById('modalCancel');
  let pendingForm   = null;

  document.querySelectorAll('.btn-confirm-delete').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      pendingForm = this.closest('form');
      if (modalMsg) modalMsg.textContent = this.dataset.msg || 'Data ini akan dihapus secara permanen.';
      if (modal) modal.classList.add('show');
    });
  });
  if (modalConfirm) modalConfirm.addEventListener('click', () => { if (pendingForm) pendingForm.submit(); });
  if (modalCancel)  modalCancel.addEventListener('click',  () => { modal.classList.remove('show'); pendingForm = null; });
  if (modal) modal.addEventListener('click', (e) => { if (e.target === modal) { modal.classList.remove('show'); pendingForm = null; } });

  // ── Live search table ──
  const searchInput = document.getElementById('tableSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const q = this.value.toLowerCase();
      document.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }

  // ── Foto preview ──
  const fotoInput = document.getElementById('fotoInput');
  const fotoPreview = document.getElementById('fotoPreview');
  if (fotoInput && fotoPreview) {
    fotoInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => { fotoPreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`; };
        reader.readAsDataURL(file);
      }
    });
    document.getElementById('uploadArea')?.addEventListener('click', () => fotoInput.click());
  }

  // ── Auto-hide alert ──
  document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity 0.5s'; setTimeout(() => el.remove(), 500); }, 4000);
  });

  // ── Toggle aktif via AJAX ──
  document.querySelectorAll('.toggle-aktif').forEach(toggle => {
    toggle.addEventListener('change', function () {
      const id     = this.dataset.id;
      const aktif  = this.checked ? 1 : 0;
      fetch('ajax_toggle.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&aktif=${aktif}`
      });
    });
  });

});