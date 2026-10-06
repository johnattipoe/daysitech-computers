/** Render the browser-saved product wishlist in the customer account. */
(function () {
  'use strict';
  const KEY = 'dtc_wishlist';

  function readItems() {
    try {
      const items = JSON.parse(localStorage.getItem(KEY) || '[]');
      return Array.isArray(items) ? items : [];
    } catch (_) { return []; }
  }

  function safeProductUrl(value) {
    try {
      const url = new URL(value || '/', window.location.origin);
      return url.origin === window.location.origin && url.pathname.startsWith('/products/') ? url.href : '/products';
    } catch (_) { return '/products'; }
  }

  function safeImageUrl(value) {
    if (!value) return '';
    try {
      const url = new URL(value, window.location.origin);
      return ['http:', 'https:'].includes(url.protocol) ? url.href : '';
    } catch (_) { return ''; }
  }

  function render() {
    const grid = document.querySelector('[data-wishlist-grid]');
    const empty = document.querySelector('[data-wishlist-empty]');
    if (!grid || !empty) return;
    const items = readItems();
    grid.replaceChildren();
    empty.hidden = items.length > 0;
    items.forEach((item) => {
      if (!item || !item.id) return;
      const card = document.createElement('article');
      card.className = 'product-card wishlist-saved-card';
      const link = document.createElement('a');
      link.className = 'product-thumb';
      link.href = safeProductUrl(item.url);
      const imageUrl = safeImageUrl(item.image);
      if (imageUrl) {
        const image = document.createElement('img');
        image.src = imageUrl;
        image.alt = String(item.name || 'Saved product');
        image.loading = 'lazy';
        link.appendChild(image);
      } else {
        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-laptop';
        icon.setAttribute('aria-hidden', 'true');
        link.appendChild(icon);
      }
      card.appendChild(link);

      const body = document.createElement('div');
      body.className = 'product-body';
      const eyebrow = document.createElement('div');
      eyebrow.className = 'product-category';
      eyebrow.textContent = 'Saved product';
      body.appendChild(eyebrow);
      const name = document.createElement('a');
      name.className = 'product-name';
      name.href = safeProductUrl(item.url);
      name.textContent = String(item.name || 'Product');
      body.appendChild(name);
      const price = document.createElement('div');
      price.className = 'product-price';
      const priceLabel = document.createElement('span');
      priceLabel.className = 'price-now';
      priceLabel.textContent = String(item.priceLabel || '');
      price.appendChild(priceLabel);
      body.appendChild(price);

      const actions = document.createElement('div');
      actions.className = 'product-actions';
      if (item.inStock) {
        const form = document.createElement('form');
        form.action = '/cart/add';
        form.method = 'POST';
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_csrf';
        csrf.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const productId = document.createElement('input');
        productId.type = 'hidden';
        productId.name = 'product_id';
        productId.value = String(item.id);
        const add = document.createElement('button');
        add.type = 'submit';
        add.className = 'btn btn-copper';
        add.innerHTML = '<i class="fa-solid fa-cart-plus me-1" aria-hidden="true"></i>Add to Cart';
        form.append(csrf, productId, add);
        actions.appendChild(form);
      }
      const remove = document.createElement('button');
      remove.type = 'button';
      remove.className = 'btn btn-outline-secondary';
      remove.textContent = 'Remove';
      remove.setAttribute('aria-label', `Remove ${String(item.name || 'product')} from wishlist`);
      remove.addEventListener('click', () => {
        const remaining = readItems().filter((saved) => String(saved.id) !== String(item.id));
        try { localStorage.setItem(KEY, JSON.stringify(remaining)); } catch (_) {}
        window.dispatchEvent(new CustomEvent('dtc:wishlist-change'));
      });
      actions.appendChild(remove);
      body.appendChild(actions);
      card.appendChild(body);
      grid.appendChild(card);
    });
    empty.hidden = grid.children.length > 0;
  }

  document.addEventListener('DOMContentLoaded', render);
  window.addEventListener('storage', render);
  window.addEventListener('dtc:wishlist-change', render);
})();