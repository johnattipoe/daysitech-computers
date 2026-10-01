/**
 * Daysitech Computers — admin.js
 * Admin-panel-only behaviours. Shared toasts/confirm/flash handling
 * already come from app.js (loaded first).
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    // Mobile sidebar toggle
    const topbar = document.querySelector('.admin-topbar');
    const sidebar = document.querySelector('.admin-sidebar');
    if (topbar && sidebar && window.innerWidth < 993) {
      const btn = document.createElement('button');
      btn.className = 'btn btn-sm btn-ink d-lg-none';
      btn.innerHTML = '<i class="fa-solid fa-bars"></i>';
      btn.addEventListener('click', () => sidebar.classList.toggle('is-open'));
      topbar.prepend(btn);
    }

    // Auto-generate a slug preview under "name" fields where present (products, categories)
    document.querySelectorAll('input[name="name"]').forEach((input) => {
      const hint = input.closest('.mb-3')?.querySelector('.slug-hint');
      if (!hint) return;
      input.addEventListener('input', () => {
        hint.textContent = input.value
          .toLowerCase()
          .trim()
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/(^-|-$)/g, '');
      });
    });

    // Quick client-side filter for any table with [data-table-search]
    document.querySelectorAll('[data-table-search]').forEach((input) => {
      const table = document.querySelector(input.dataset.tableSearch);
      if (!table) return;
      input.addEventListener('input', () => {
        const term = input.value.toLowerCase();
        table.querySelectorAll('tbody tr, tr').forEach((row) => {
          row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
      });
    });
  });
})();
