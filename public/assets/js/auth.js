/**
 * Daysitech Computers — auth.js
 * Small UX helpers for login/register forms.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const pwd = document.querySelector('input[name="password"]');
    const confirm = document.querySelector('input[name="password_confirmation"]');

    if (pwd && confirm) {
      const check = () => {
        confirm.setCustomValidity(confirm.value && confirm.value !== pwd.value ? 'Passwords do not match' : '');
      };
      pwd.addEventListener('input', check);
      confirm.addEventListener('input', check);
    }

    // Simple strength hint on the register form
    if (pwd && document.querySelector('form[action="/register"]')) {
      let hint = document.createElement('div');
      hint.className = 'small mt-1';
      pwd.insertAdjacentElement('afterend', hint);
      pwd.addEventListener('input', () => {
        const v = pwd.value;
        const score = [v.length >= 8, /[A-Z]/.test(v), /[0-9]/.test(v), /[^A-Za-z0-9]/.test(v)].filter(Boolean).length;
        const labels = ['Too short', 'Weak', 'Okay', 'Good', 'Strong'];
        const colors = ['#E0542C', '#E0542C', '#E3A73B', '#2FBE85', '#2FBE85'];
        hint.textContent = v ? labels[score] : '';
        hint.style.color = colors[score];
      });
    }
  });
})();
