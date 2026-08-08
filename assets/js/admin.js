/* True Chain admin behaviour */
(function () {
  'use strict';

  /* ---------- mobile sidebar ---------- */
  var st = document.getElementById('sideToggle');
  if (st) st.addEventListener('click', function () { document.body.classList.toggle('side-open'); });

  /* ---------- delete confirmations ---------- */
  document.querySelectorAll('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (!window.confirm(f.getAttribute('data-confirm') || 'Are you sure?')) e.preventDefault();
    });
  });

  /* ---------- slug helper ---------- */
  var titleInput = document.querySelector('[data-slug-source]');
  var slugInput = document.querySelector('[data-slug-target]');
  if (titleInput && slugInput) {
    titleInput.addEventListener('input', function () {
      if (slugInput.dataset.touched === '1' || slugInput.readOnly) return;
      slugInput.value = titleInput.value.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    });
    slugInput.addEventListener('input', function () { slugInput.dataset.touched = '1'; });
  }

  /* ---------- media fields: preview + browse popup ---------- */
  window.__setMediaField = function (fieldId, path) {
    var input = document.getElementById(fieldId);
    if (input) {
      input.value = path;
      input.dispatchEvent(new Event('change'));
    }
  };
  document.querySelectorAll('.media-field').forEach(function (wrap) {
    var input = wrap.querySelector('input');
    var preview = wrap.querySelector('.media-preview');
    var browse = wrap.querySelector('[data-browse]');
    var refresh = function () {
      if (!preview) return;
      var v = (input.value || '').trim();
      var base = document.body.getAttribute('data-base') || '';
      var src = '';
      if (v) {
        if (/^(https?:)?\/\//i.test(v) || v.indexOf('data:') === 0) src = v;
        else if (v.indexOf('assets/') === 0) src = base + '/' + v;
        else src = base + '/uploads/' + v.replace(/^\/+/, '');
      }
      preview.style.backgroundImage = src ? 'url("' + src + '")' : 'none';
    };
    if (input) { input.addEventListener('change', refresh); input.addEventListener('input', refresh); refresh(); }
    if (browse && input) {
      browse.addEventListener('click', function () {
        var base = document.body.getAttribute('data-base') || '';
        window.open(base + '/admin?r=media&picker=1&target=' + encodeURIComponent(input.id),
          'mediaPicker', 'width=980,height=720,scrollbars=yes');
      });
    }
  });

  /* ---------- rich editors (Quill + HTML tab) ---------- */
  document.querySelectorAll('.editor-wrap').forEach(function (wrap) {
    var textarea = wrap.querySelector('textarea');
    var visual = wrap.querySelector('.editor-visual');
    var tabV = wrap.querySelector('[data-tab="visual"]');
    var tabH = wrap.querySelector('[data-tab="html"]');
    if (!textarea || !visual || typeof Quill === 'undefined') return;

    var hasLayout = /class\s*=|<svg|style\s*=/.test(textarea.value);
    if (hasLayout) wrap.classList.add('has-layout');

    var quill = new Quill(visual, {
      theme: 'snow',
      modules: {
        toolbar: [
          [{ header: [2, 3, false] }],
          ['bold', 'italic', 'underline'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['link', 'blockquote'],
          ['clean']
        ]
      }
    });
    quill.clipboard.dangerouslyPasteHTML(textarea.value || '');

    var inHtml = false;
    var syncFromQuill = function () { textarea.value = quill.root.innerHTML; };
    var syncToQuill = function () { quill.clipboard.dangerouslyPasteHTML(textarea.value || ''); };

    if (hasLayout) {
      /* default to HTML mode to protect designed markup */
      wrap.classList.add('html-mode'); inHtml = true;
      if (tabH) tabH.classList.add('active');
    } else if (tabV) {
      tabV.classList.add('active');
    }

    if (tabV) tabV.addEventListener('click', function () {
      if (!inHtml) return;
      inHtml = false; wrap.classList.remove('html-mode');
      tabV.classList.add('active'); if (tabH) tabH.classList.remove('active');
      syncToQuill();
    });
    if (tabH) tabH.addEventListener('click', function () {
      if (inHtml) return;
      inHtml = true; wrap.classList.add('html-mode');
      tabH.classList.add('active'); if (tabV) tabV.classList.remove('active');
      syncFromQuill();
    });

    var form = wrap.closest('form');
    if (form) form.addEventListener('submit', function () { if (!inHtml) syncFromQuill(); });
  });

  /* ---------- settings: maintenance toggle warning ---------- */
  var mm = document.getElementById('maintenance_mode');
  if (mm) {
    mm.addEventListener('change', function () {
      if (mm.checked) alert('Heads up: with maintenance mode ON, visitors will see a maintenance page. You will still be able to browse the site while signed in.');
    });
  }
})();
