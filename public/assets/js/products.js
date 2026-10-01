/**
 * Daysitech Computers — products.js
 * Product listing/detail enhancements: compare tray, AJAX add-to-cart.
 */
(function () {
  'use strict';

  const KEY = 'dtc_compare_ids';
  const getCompare = () => JSON.parse(sessionStorage.getItem(KEY) || '[]');
  const setCompare = (ids) => sessionStorage.setItem(KEY, JSON.stringify(ids));

  document.addEventListener('DOMContentLoaded', function () {
    // AJAX add-to-cart on product cards (progressive enhancement over the normal form POST)
    document.querySelectorAll('.product-card form[action="/cart/add"]').forEach((form) => {
      form.addEventListener('submit', function (e) {
        if (!window.fetch) return; // fall back to normal form submit
        e.preventDefault();
        const fd = new FormData(form);
        fd.append('ajax', '1');
        fetch('/cart/add', { method: 'POST', body: fd, headers: { Accept: 'application/json' } })
          .then((r) => r.json())
          .then((data) => {
            window.DTC?.toast(data.message, data.success ? 'success' : 'error');
            if (data.success) window.DTC?.updateCartBadge();
          })
          .catch(() => form.submit());
      });
    });

    // Wishlist heart toggle (visual only — persisted wishlist is a future enhancement)
    document.querySelectorAll('.wishlist-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        const icon = btn.querySelector('i');
        icon.classList.toggle('fa-regular');
        icon.classList.toggle('fa-solid');
        icon.style.color = icon.classList.contains('fa-solid') ? '#E0542C' : '';
      });
    });
  });
})();
