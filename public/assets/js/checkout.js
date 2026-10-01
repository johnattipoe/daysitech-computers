/**
 * Daysitech Computers — checkout.js
 * Highlights the selected payment method card.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const options = document.querySelectorAll('.payment-option');
    const sync = () => options.forEach((o) => o.classList.toggle('is-selected', o.querySelector('input')?.checked));
    options.forEach((o) => o.querySelector('input')?.addEventListener('change', sync));
    sync();
  });
})();
