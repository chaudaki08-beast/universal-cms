/* Universal CMS — front-end behaviour */
(function () {
  'use strict';

  // Auto-dismiss toasts
  document.querySelectorAll('.cms-toast').forEach(function (t) {
    setTimeout(function () {
      t.style.transition = 'opacity .4s, transform .4s';
      t.style.opacity = '0';
      t.style.transform = 'translateX(40px)';
      setTimeout(function () { t.remove(); }, 400);
    }, 4500);
  });

  // Simple lightbox for gallery images
  document.querySelectorAll('[data-lightbox]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      e.preventDefault();
      var overlay = document.createElement('div');
      overlay.className = 'cms-lightbox';
      overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.85);display:flex;align-items:center;justify-content:center;z-index:2000;cursor:zoom-out;padding:30px';
      overlay.innerHTML = '<img src="' + a.getAttribute('href') + '" style="max-width:92%;max-height:92%;border-radius:8px">';
      overlay.addEventListener('click', function () { overlay.remove(); });
      document.body.appendChild(overlay);
    });
  });

  // Transparent header -> solid on scroll
  var transHeader = document.querySelector('.site-header.is-transparent');
  if (transHeader) {
    var onScroll = function () {
      transHeader.classList.toggle('scrolled', window.scrollY > 40);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // Scroll reveal
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.style.opacity = 1; en.target.style.transform = 'none'; io.unobserve(en.target); }
      });
    }, { threshold: 0.08 });
    document.querySelectorAll('.cms-section').forEach(function (s) {
      s.style.opacity = 0; s.style.transform = 'translateY(24px)';
      s.style.transition = 'opacity .6s ease, transform .6s ease';
      io.observe(s);
    });
  }
})();
