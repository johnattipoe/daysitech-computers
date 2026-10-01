/**
 * Daysitech Computers — cart.js
 * Cart page quantity stepper + live totals feel (server remains source of truth).
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cart-row .qty-stepper input').forEach((input) => {
      input.addEventListener('change', () => {
        if (parseInt(input.value, 10) < 1) input.value = 1;
      });
    });
  });
})();
