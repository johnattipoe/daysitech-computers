/**
 * Daysitech Computers — auth.js
 * Small UX helpers for login/register forms.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const adminLoginForm = document.querySelector('form[data-admin-login]');
    if (adminLoginForm) {
      const returnToStore = () => window.location.replace('/');
      adminLoginForm.addEventListener('submit', () => {
        const tabName = 'daysitech-admin-panel';
        const adminTab = window.open('', tabName);
        if (adminTab) {
          adminLoginForm.target = tabName;
          try {
            adminTab.document.title = 'Daysitech Admin';
            adminTab.document.body.textContent = 'Signing in to the admin panel…';
          } catch (_) {}
        } else {
          // Retain the native new-tab form target if script-created tabs are blocked.
          adminLoginForm.target = '_blank';
        }
      });
      window.addEventListener('message', (event) => {
        if (event.origin !== window.location.origin || event.data?.type !== 'daysitech-admin-login-success') return;
        returnToStore();
      });
      window.addEventListener('storage', (event) => {
        if (event.key !== 'daysitech-admin-login-success' || !event.newValue) return;
        try { localStorage.removeItem('daysitech-admin-login-success'); } catch (_) {}
        returnToStore();
      });
      if ('BroadcastChannel' in window) {
        const channel = new BroadcastChannel('daysitech-admin-login');
        channel.addEventListener('message', (event) => {
          if (event.data?.type === 'success') returnToStore();
        });
      }
    }

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
