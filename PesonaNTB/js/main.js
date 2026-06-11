document.addEventListener('DOMContentLoaded', function () {

  // ── Navbar mobile toggle ──
  const navbar   = document.querySelector('.navbar');
  const toggle   = document.getElementById('navToggle');
  if (toggle && navbar) {
    toggle.addEventListener('click', () => navbar.classList.toggle('open'));
    document.addEventListener('click', (e) => {
      if (!navbar.contains(e.target)) navbar.classList.remove('open');
    });
  }

  // ── User dropdown menu ──
  const userBtn      = document.getElementById('userMenuBtn');
  const userDropdown = document.getElementById('userDropdown');
  if (userBtn && userDropdown) {
    userBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      userDropdown.classList.toggle('open');
    });
    document.addEventListener('click', () => userDropdown.classList.remove('open'));
  }

  // ── Navbar scroll shadow ──
  window.addEventListener('scroll', () => {
    if (navbar) {
      navbar.style.boxShadow = window.scrollY > 10 ? '0 2px 16px rgba(92,61,46,0.10)' : 'none';
    }
  });

  // ── Smooth scroll for anchor links ──
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ── Intersection Observer: fade-in on scroll ──
  const fadeEls = document.querySelectorAll('.dest-card, .kat-card, .testi-card, .section-header');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  fadeEls.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(el);
  });

});