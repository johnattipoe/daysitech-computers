/**
 * Daysitech Computers — app.js
 * Global behaviours shared across every storefront page:
 * mobile nav, flash-message toasts, cart badge sync, confirm dialogs.
 */
(function () {
  'use strict';

  const DTC = (window.DTC = window.DTC || {});

  DTC.hidePageLoader = function () {
    const loader = document.getElementById('pageLoader');
    if (!loader) return;
    loader.classList.add('is-hidden');
    window.setTimeout(() => loader.remove(), 500);
  };

  DTC.csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

  DTC.toast = function (message, icon = 'success') {
    if (window.Swal) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon,
        title: message,
        showConfirmButton: false,
        timer: 3200,
        timerProgressBar: true,
      });
    } else {
      alert(message);
    }
  };

  DTC.confirm = function (message, opts = {}) {
    if (!window.Swal) return Promise.resolve(confirm(message));
    return Swal.fire({
      title: opts.title || 'Are you sure?',
      text: message,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#C9793D',
      cancelButtonColor: '#8896AC',
      confirmButtonText: opts.confirmText || 'Yes, continue',
    }).then((r) => r.isConfirmed);
  };

  DTC.updateCartBadge = function () {
    fetch('/cart/count')
      .then((r) => r.json())
      .then((data) => {
        document.querySelectorAll('.cart-count').forEach((el) => {
          el.textContent = data.count || 0;
          el.style.display = data.count > 0 ? 'flex' : 'none';
        });
      })
      .catch(() => {});
  };

  document.addEventListener('DOMContentLoaded', function () {
    DTC.hidePageLoader();

    // Auto-dismiss server-rendered flash alerts
    document.querySelectorAll('[data-flash]').forEach((el) => {
      const type = el.dataset.flash === 'error' ? 'error' : 'success';
      DTC.toast(el.dataset.message, type);
    });

    DTC.updateCartBadge();

    const categorySearch = document.getElementById('categorySearch');
    const categoryCards = Array.from(document.querySelectorAll('[data-category-card]'));
    const categoryGrid = document.getElementById('categoryGrid');
    const categoryCount = document.getElementById('categoryResultCount');
    const categoryNoResults = document.getElementById('categoryNoResults');

    if (categorySearch && categoryCards.length && categoryGrid && categoryCount && categoryNoResults) {
      const filterCategories = () => {
        const query = categorySearch.value.trim().toLocaleLowerCase();
        const visibleCount = categoryCards.reduce((count, card) => {
          const matches = card.dataset.categoryName.includes(query);
          card.hidden = !matches;
          return count + Number(matches);
        }, 0);

        categoryCount.textContent = `${visibleCount} ${visibleCount === 1 ? 'category' : 'categories'}`;
        categoryGrid.hidden = visibleCount === 0;
        categoryNoResults.hidden = visibleCount !== 0;
      };

      categorySearch.addEventListener('input', filterCategories);
      document.getElementById('categoryClearSearch')?.addEventListener('click', () => {
        categorySearch.value = '';
        filterCategories();
        categorySearch.focus();
      });
    }

    // Confirm-before-submit forms (delete/cancel actions)
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        DTC.confirm(form.dataset.confirm).then((ok) => ok && form.submit());
      });
    });

    // Mobile off-canvas nav toggle (if Bootstrap's data-bs isn't enough)
    const navToggle = document.querySelector('.nav-toggle');
    const mobileMenu = document.getElementById('mobileMenu');
    if (navToggle && mobileMenu && window.bootstrap) {
      navToggle.addEventListener('click', () => {
        bootstrap.Offcanvas.getOrCreateInstance(mobileMenu).toggle();
      });
    }

    // Newsletter / generic AJAX forms with data-ajax
    document.querySelectorAll('form[data-ajax]').forEach((form) => {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(form);
        fetch(form.action, { method: form.method || 'POST', body: fd, headers: { Accept: 'application/json' } })
          .then((r) => r.json())
          .then((data) => {
            DTC.toast(data.message || (data.success ? 'Done!' : 'Something went wrong.'), data.success ? 'success' : 'error');
            if (data.success && form.dataset.resetOnSuccess !== 'false') form.reset();
          })
          .catch(() => DTC.toast('Network error. Please try again.', 'error'));
      });
    });
  });
})();
