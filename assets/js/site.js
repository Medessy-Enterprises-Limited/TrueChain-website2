/* True Chain Infrastructure Company — public site behaviour */
(function () {
  'use strict';

  /* ---------- sticky header state ---------- */
  var header = document.getElementById('siteHeader');
  var onScroll = function () {
    if (header) header.classList.toggle('scrolled', window.scrollY > 8);
    var btt = document.getElementById('backToTop');
    if (btt) btt.classList.toggle('show', window.scrollY > 600);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- mobile nav ---------- */
  var toggle = document.getElementById('navToggle');
  if (toggle) {
    toggle.addEventListener('click', function () {
      var open = document.body.classList.toggle('nav-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.querySelectorAll('#mainNav a').forEach(function (a) {
      a.addEventListener('click', function () {
        document.body.classList.remove('nav-open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  /* ---------- back to top ---------- */
  var btt = document.getElementById('backToTop');
  if (btt) {
    btt.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ---------- hero slider ---------- */
  var hero = document.querySelector('[data-slider]');
  if (hero) {
    var slides = Array.prototype.slice.call(hero.querySelectorAll('.hero-slide'));
    var dots = Array.prototype.slice.call(hero.querySelectorAll('.hero-dot'));
    var idx = 0;
    var timer = null;
    var INTERVAL = 6500;

    var show = function (n) {
      idx = (n + slides.length) % slides.length;
      slides.forEach(function (s, i) { s.classList.toggle('active', i === idx); });
      dots.forEach(function (d, i) {
        d.classList.toggle('active', i === idx);
        d.setAttribute('aria-current', i === idx ? 'true' : 'false');
      });
    };
    var next = function () { show(idx + 1); };
    var start = function () {
      if (slides.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        stop();
        timer = window.setInterval(next, INTERVAL);
      }
    };
    var stop = function () { if (timer) { clearInterval(timer); timer = null; } };

    dots.forEach(function (d, i) {
      d.addEventListener('click', function () { show(i); start(); });
    });
    var prevBtn = hero.querySelector('[data-prev]');
    var nextBtn = hero.querySelector('[data-next]');
    if (prevBtn) prevBtn.addEventListener('click', function () { show(idx - 1); start(); });
    if (nextBtn) nextBtn.addEventListener('click', function () { next(); start(); });

    hero.addEventListener('mouseenter', stop);
    hero.addEventListener('mouseleave', start);

    /* swipe support */
    var touchX = null;
    hero.addEventListener('touchstart', function (e) { touchX = e.touches[0].clientX; }, { passive: true });
    hero.addEventListener('touchend', function (e) {
      if (touchX === null) return;
      var dx = e.changedTouches[0].clientX - touchX;
      if (Math.abs(dx) > 50) { dx > 0 ? show(idx - 1) : next(); start(); }
      touchX = null;
    }, { passive: true });

    show(0);
    start();
  }

  /* ---------- reveal on scroll ---------- */
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) {
          en.target.classList.add('in');
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
  } else {
    document.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('in'); });
  }

  /* ---------- leader bio expand ---------- */
  document.querySelectorAll('[data-bio-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var bio = btn.parentElement.querySelector('.leader-bio');
      var clamped = bio.classList.toggle('clamp');
      btn.textContent = clamped ? 'Read more' : 'Read less';
    });
  });

  /* ---------- contact form: stamp render time ---------- */
  var ts = document.getElementById('form_ts');
  if (ts && !ts.value) ts.value = Math.floor(Date.now() / 1000);
})();
