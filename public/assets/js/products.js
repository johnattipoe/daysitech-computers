/** Storefront product interactions: AJAX cart and browser-saved wishlist. */
(function () {
  'use strict';
  const WISHLIST_KEY = 'dtc_wishlist';
  const COMPARE_KEY = 'dtc_compare_ids';
  const MAX_COMPARE = 3;

  function readCompare() {
    try {
      const ids = JSON.parse(sessionStorage.getItem(COMPARE_KEY) || '[]');
      return Array.isArray(ids) ? ids.map(String).slice(0, MAX_COMPARE) : [];
    } catch (_) { return []; }
  }

  function initCompare() {
    const buttons = document.querySelectorAll('[data-compare-product]');
    if (!buttons.length) return;
    const tray = document.createElement('div');
    tray.className = 'product-compare-tray';
    tray.setAttribute('aria-live', 'polite');
    const label = document.createElement('span');
    const link = document.createElement('a');
    link.className = 'btn btn-copper btn-sm';
    link.textContent = 'Compare';
    const clear = document.createElement('button');
    clear.type = 'button';
    clear.className = 'btn btn-outline-secondary btn-sm';
    clear.textContent = 'Clear';
    tray.append(label, link, clear);
    document.body.appendChild(tray);

    const update = () => {
      const ids = readCompare();
      buttons.forEach((button) => button.setAttribute('aria-pressed', String(ids.includes(String(button.dataset.compareProduct)))));
      label.textContent = `${ids.length} of ${MAX_COMPARE} products selected`;
      tray.hidden = ids.length === 0;
      link.href = `/products/compare?ids=${encodeURIComponent(ids.join(','))}`;
      link.setAttribute('aria-disabled', String(ids.length < 2));
      link.tabIndex = ids.length < 2 ? -1 : 0;
      link.classList.toggle('disabled', ids.length < 2);
    };

    buttons.forEach((button) => button.addEventListener('click', () => {
      let ids = readCompare();
      const id = String(button.dataset.compareProduct || '');
      if (!id) return;
      if (ids.includes(id)) ids = ids.filter((saved) => saved !== id);
      else if (ids.length >= MAX_COMPARE) {
        window.DTC?.toast(`Choose up to ${MAX_COMPARE} products to compare.`, 'info');
        return;
      } else ids.push(id);
      try { sessionStorage.setItem(COMPARE_KEY, JSON.stringify(ids)); } catch (_) {}
      update();
    }));
    clear.addEventListener('click', () => {
      try { sessionStorage.removeItem(COMPARE_KEY); } catch (_) {}
      update();
    });
    update();
  }

  function readWishlist() {
    try {
      const parsed = JSON.parse(localStorage.getItem(WISHLIST_KEY) || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (_) { return []; }
  }

  function saveWishlist(items) {
    try {
      localStorage.setItem(WISHLIST_KEY, JSON.stringify(items));
      window.dispatchEvent(new CustomEvent('dtc:wishlist-change'));
      return true;
    } catch (_) {
      window.DTC?.toast('Your browser could not save this wishlist.', 'error');
      return false;
    }
  }

  function syncWishlistButtons() {
    const savedIds = new Set(readWishlist().map((item) => String(item.id)));
    document.querySelectorAll('[data-wishlist-product]').forEach((button) => {
      const active = savedIds.has(String(button.dataset.productId));
      button.setAttribute('aria-pressed', String(active));
      button.setAttribute('aria-label', `${active ? 'Remove' : 'Save'} ${button.dataset.productName} ${active ? 'from' : 'to'} wishlist`);
      const icon = button.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-solid', active);
        icon.classList.toggle('fa-regular', !active);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.product-card form[action="/cart/add"]').forEach((form) => {
      form.addEventListener('submit', function (event) {
        if (!window.fetch) return;
        event.preventDefault();
        const data = new FormData(form);
        data.append('ajax', '1');
        fetch('/cart/add', { method: 'POST', body: data, headers: { Accept: 'application/json' } })
          .then((response) => response.json())
          .then((result) => {
            window.DTC?.toast(result.message, result.success ? 'success' : 'error');
            if (result.success) window.DTC?.updateCartBadge();
          })
          .catch(() => form.submit());
      });
    });

    document.querySelectorAll('[data-wishlist-product]').forEach((button) => {
      button.addEventListener('click', () => {
        const item = {
          id: button.dataset.productId,
          name: button.dataset.productName,
          url: button.dataset.productUrl,
          image: button.dataset.productImage,
          priceLabel: button.dataset.productPriceLabel,
          inStock: button.dataset.productInStock === 'true',
        };
        if (!item.id) return;
        const items = readWishlist();
        const index = items.findIndex((saved) => String(saved.id) === String(item.id));
        if (index >= 0) items.splice(index, 1);
        else items.push(item);
        if (saveWishlist(items)) syncWishlistButtons();
      });
    });
    syncWishlistButtons();
    window.addEventListener('storage', syncWishlistButtons);
    window.addEventListener('dtc:wishlist-change', syncWishlistButtons);
    initCompare();
  });
})();
