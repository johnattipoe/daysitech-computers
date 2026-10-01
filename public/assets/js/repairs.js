/**
 * Daysitech Computers — repairs.js
 * Repair booking form + ticket tracking helpers.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    // Uppercase ticket number as the customer types it on the tracking form
    const ticketInput = document.querySelector('input[name="ticket"]');
    if (ticketInput) {
      ticketInput.addEventListener('input', () => {
        ticketInput.value = ticketInput.value.toUpperCase();
      });
    }

    // Character counter for the issue description textarea
    const issue = document.querySelector('textarea[name="issue_description"]');
    if (issue) {
      const counter = document.createElement('div');
      counter.className = 'small text-muted-dtc mt-1';
      issue.insertAdjacentElement('afterend', counter);
      const update = () => (counter.textContent = `${issue.value.length} characters (minimum 10)`);
      issue.addEventListener('input', update);
      update();
    }
  });
})();
