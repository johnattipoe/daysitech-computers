/** Daysitech admin enhancements: navigation, theme and table tools. */
(function () {
  'use strict';

  const collator = new Intl.Collator(undefined, { numeric: true, sensitivity: 'base' });

  function initTheme() {
    const root = document.documentElement;
    const toggle = document.querySelector('[data-admin-theme-toggle]');
    let saved = 'light';
    try { saved = localStorage.getItem('daysitech-admin-theme') || 'light'; } catch (_) {}
    const apply = (theme) => {
      const dark = theme === 'dark';
      root.classList.toggle('admin-dark-mode', dark);
      root.dataset.bsTheme = dark ? 'dark' : 'light';
      if (toggle) {
        toggle.setAttribute('aria-pressed', String(dark));
        toggle.setAttribute('aria-label', dark ? 'Switch to light theme' : 'Switch to dark theme');
        toggle.title = dark ? 'Switch to light theme' : 'Switch to dark theme';
        const icon = toggle.querySelector('i');
        if (icon) icon.className = `fa-solid fa-${dark ? 'sun' : 'moon'}`;
      }
    };
    apply(saved);
    toggle?.addEventListener('click', () => {
      const next = root.classList.contains('admin-dark-mode') ? 'light' : 'dark';
      apply(next);
      try { localStorage.setItem('daysitech-admin-theme', next); } catch (_) {}
    });
  }

  function initNavigation() {
    const shell = document.querySelector('.admin-shell');
    const sidebar = document.querySelector('.admin-sidebar');
    const toggle = document.querySelector('[data-admin-nav-toggle]');
    const closeButton = document.querySelector('[data-admin-nav-close]');
    if (!shell || !sidebar || !toggle) return;
    const setOpen = (open) => {
      shell.classList.toggle('nav-open', open);
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
      document.body.classList.toggle('admin-nav-open', open);
    };
    toggle.addEventListener('click', () => setOpen(!shell.classList.contains('nav-open')));
    closeButton?.addEventListener('click', () => setOpen(false));
    sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') setOpen(false); });
    window.addEventListener('resize', () => { if (window.innerWidth > 991) setOpen(false); });
  }

  function csvCell(value) {
    return `"${String(value ?? '').replace(/\s+/g, ' ').replace(/"/g, '""').trim()}"`;
  }

  function initTableTools(table, index) {
    if (table.dataset.adminTools === 'off') return;
    const allRows = Array.from(table.querySelectorAll('tr'));
    const headerRow = allRows.find((row) => row.querySelector('th')) || null;
    const headerCells = headerRow ? Array.from(headerRow.cells) : [];
    const dataRows = allRows.filter((row) => row !== headerRow && row.querySelector('td'));
    if (!dataRows.length) return;

    const mount = table.closest('.admin-panel-body') || table.parentElement;
    if (!mount) return;
    const input = document.createElement('input');
    input.type = 'search';
    input.className = 'form-control form-control-sm admin-table-search';
    input.placeholder = 'Search visible rows';
    input.setAttribute('aria-label', 'Search rows in this table');
    input.autocomplete = 'off';

    const count = document.createElement('span');
    count.className = 'admin-table-count';
    count.setAttribute('role', 'status');
    count.setAttribute('aria-live', 'polite');

    const tools = document.createElement('div');
    tools.className = 'admin-table-tools';
    tools.append(input, count);

    const exportButton = document.createElement('button');
    exportButton.type = 'button';
    exportButton.className = 'btn btn-sm btn-outline-secondary admin-export-button';
    exportButton.textContent = 'Export CSV';
    exportButton.setAttribute('aria-label', 'Export visible table rows as CSV');
    tools.append(exportButton);

    const tableFrame = table.closest('.table-responsive') || table;
    mount.insertBefore(tools, tableFrame);

    const updateRows = () => {
      const term = input.value.trim().toLocaleLowerCase();
      let visible = 0;
      dataRows.forEach((row) => {
        const matches = row.textContent.toLocaleLowerCase().includes(term);
        row.hidden = !matches;
        if (matches) visible++;
      });
      count.textContent = `${visible} of ${dataRows.length} rows`;
    };
    input.addEventListener('input', updateRows);
    updateRows();

    headerCells.forEach((cell, columnIndex) => {
      const label = cell.textContent.trim();
      if (!label || /^(actions?|manage)$/i.test(label) || cell.children.length > 0) return;
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'admin-sort-button';
      button.textContent = label;
      cell.textContent = '';
      cell.append(button);
      cell.setAttribute('aria-sort', 'none');
      let direction = 0;
      button.addEventListener('click', () => {
        direction = direction === 1 ? -1 : 1;
        headerCells.forEach((other) => { if (other !== cell) other.setAttribute('aria-sort', 'none'); });
        cell.setAttribute('aria-sort', direction === 1 ? 'ascending' : 'descending');
        dataRows.sort((a, b) => {
          const left = a.cells[columnIndex]?.textContent.trim() || '';
          const right = b.cells[columnIndex]?.textContent.trim() || '';
          return collator.compare(left, right) * direction;
        });
        const parent = dataRows[0]?.parentElement || headerRow.parentElement;
        dataRows.forEach((row) => parent.appendChild(row));
        updateRows();
      });
    });

    exportButton.addEventListener('click', () => {
      const excluded = new Set(headerCells.flatMap((cell, cellIndex) => {
        const label = cell.textContent.trim();
        if (/^(actions?|manage)$/i.test(label)) return [cellIndex];
        if (!label && dataRows.every((row) => row.cells[cellIndex]?.querySelector('a, button, form, img'))) return [cellIndex];
        return [];
      }));
      const visibleRows = dataRows.filter((row) => !row.hidden);
      const output = [];
      if (headerCells.length) output.push(headerCells.flatMap((cell, i) => excluded.has(i) ? [] : [csvCell(cell.textContent)]).join(','));
      visibleRows.forEach((row) => {
        const cells = Array.from(row.cells);
        output.push(cells.flatMap((cell, i) => excluded.has(i) ? [] : [csvCell(cell.innerText || cell.textContent)]).join(','));
      });
      const blob = new Blob(['\uFEFF' + output.join('\r\n')], { type: 'text/csv;charset=utf-8' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `daysitech-admin-${index + 1}-${new Date().toISOString().slice(0, 10)}.csv`;
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.setTimeout(() => URL.revokeObjectURL(url), 1000);
    });
  }

  function initUnsavedWarnings() {
    const dirtyForms = new Set();
    document.querySelectorAll('form[data-unsaved-warning]').forEach((form) => {
      form.addEventListener('input', () => dirtyForms.add(form));
      form.addEventListener('change', () => dirtyForms.add(form));
      form.addEventListener('submit', () => dirtyForms.delete(form));
    });
    window.addEventListener('beforeunload', (event) => {
      if (!dirtyForms.size) return;
      event.preventDefault();
      event.returnValue = '';
    });
  }

  function initPasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
      const input = button.closest('.admin-password-field')?.querySelector('[data-admin-password]');
      if (!input) return;
      button.addEventListener('click', () => {
        const showing = input.type === 'password';
        input.type = showing ? 'text' : 'password';
        button.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
        button.innerHTML = `<i class="fa-solid fa-eye${showing ? '-slash' : ''}" aria-hidden="true"></i>`;
      });
    });
  }

  function initCopyButtons() {
    document.querySelectorAll('[data-copy-value]').forEach((button) => {
      button.addEventListener('click', async () => {
        const value = button.dataset.copyValue || '';
        try {
          if (navigator.clipboard && window.isSecureContext) await navigator.clipboard.writeText(value);
          else {
            const helper = document.createElement('textarea');
            helper.value = value;
            helper.style.position = 'fixed';
            helper.style.opacity = '0';
            document.body.appendChild(helper);
            helper.select();
            document.execCommand('copy');
            helper.remove();
          }
          const original = button.innerHTML;
          button.innerHTML = '<i class="fa-solid fa-check" aria-hidden="true"></i> Copied';
          window.setTimeout(() => { button.innerHTML = original; }, 1600);
        } catch (_) {
          button.setAttribute('title', 'Copy failed. Select and copy the reference manually.');
        }
      });
    });
  }

  function initProductEditors() {
    document.querySelectorAll('[data-product-editor]').forEach((form) => {
      const imagesField = form.querySelector('[name="images"]');
      if (imagesField) {
        const preview = document.createElement('div');
        preview.className = 'admin-image-preview';
        preview.setAttribute('aria-label', 'Product image previews');
        imagesField.insertAdjacentElement('afterend', preview);
        const drawPreviews = () => {
          preview.replaceChildren();
          imagesField.value.split(/\r?\n/).map((line) => line.trim()).filter(Boolean).slice(0, 6).forEach((source) => {
            try {
              const parsed = new URL(source, window.location.href);
              if (!['http:', 'https:'].includes(parsed.protocol)) return;
              const image = document.createElement('img');
              image.src = parsed.href;
              image.alt = 'Product image preview';
              image.loading = 'lazy';
              image.addEventListener('error', () => image.remove(), { once: true });
              preview.appendChild(image);
            } catch (_) {}
          });
        };
        imagesField.addEventListener('input', drawPreviews);
        drawPreviews();
      }

      const rows = form.querySelector('[data-spec-rows]');
      if (!rows) return;
      rows.querySelectorAll('.row').forEach((row) => {
        const key = row.querySelector('[name="spec_key[]"]')?.closest('[class*="col-"]');
        const value = row.querySelector('[name="spec_value[]"]')?.closest('[class*="col-"]');
        if (key) key.className = 'col-12 col-md-4';
        if (value) value.className = 'col-12 col-md-7';
        if (!row.querySelector('[data-remove-spec]')) {
          const removeColumn = document.createElement('div');
          removeColumn.className = 'col-12 col-md-1';
          const remove = document.createElement('button');
          remove.type = 'button';
          remove.className = 'btn btn-sm btn-outline-secondary w-100';
          remove.dataset.removeSpec = '';
          remove.setAttribute('aria-label', 'Remove specification');
          remove.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
          removeColumn.appendChild(remove);
          row.appendChild(removeColumn);
        }
      });
      const add = document.createElement('button');
      add.type = 'button';
      add.className = 'btn btn-sm btn-outline-secondary';
      add.innerHTML = '<i class="fa-solid fa-plus me-1" aria-hidden="true"></i> Add specification';
      rows.insertAdjacentElement('afterend', add);
      add.addEventListener('click', () => {
        const row = rows.querySelector('.row')?.cloneNode(true);
        if (!row) return;
        row.querySelectorAll('input').forEach((input) => { input.value = ''; input.removeAttribute('value'); });
        rows.appendChild(row);
      });
      rows.addEventListener('click', (event) => {
        const remove = event.target.closest('[data-remove-spec]');
        if (!remove) return;
        const row = remove.closest('.row');
        if (rows.querySelectorAll('.row').length === 1) row.querySelectorAll('input').forEach((input) => { input.value = ''; });
        else row.remove();
      });
    });
  }

  function initStockPreviews() {
    document.querySelectorAll('[data-stock-preview]').forEach((form) => {
      const product = form.querySelector('[name="product_id"]');
      const mode = form.querySelector('[data-stock-mode]');
      const quantity = form.querySelector('[data-stock-quantity]');
      if (!product || !mode || !quantity) return;
      const preview = document.createElement('p');
      preview.className = 'admin-stock-preview';
      preview.setAttribute('role', 'status');
      quantity.closest('.mb-3')?.insertAdjacentElement('afterend', preview);
      const update = () => {
        const option = product.selectedOptions[0];
        const current = Number(option?.dataset.stock);
        const amount = Number(quantity.value);
        quantity.min = mode.value === 'set' ? '0' : '1';
        if (!option?.value || !Number.isFinite(current)) { preview.textContent = 'Select a product to preview the stock change.'; return; }
        const projected = mode.value === 'set' ? amount : current + amount;
        preview.textContent = `Current stock: ${current} · New stock: ${Number.isFinite(amount) ? projected : '—'}`;
      };
      [product, mode, quantity].forEach((field) => field.addEventListener('input', update));
      [product, mode].forEach((field) => field.addEventListener('change', update));
      update();
    });
  }

  function documentReady() {
    initTheme();
    initNavigation();
    initUnsavedWarnings();
    initPasswordToggles();
    initCopyButtons();
    initProductEditors();
    initStockPreviews();

    document.querySelectorAll('input[name="name"]').forEach((input) => {
      const hint = input.closest('.mb-3')?.querySelector('.slug-hint');
      if (!hint) return;
      input.addEventListener('input', () => {
        hint.textContent = input.value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
      });
    });

    document.querySelectorAll('[data-table-search]').forEach((input) => {
      const table = document.querySelector(input.dataset.tableSearch);
      if (!table) return;
      input.addEventListener('input', () => {
        const term = input.value.toLocaleLowerCase();
        table.querySelectorAll('tbody tr, tr').forEach((row) => {
          row.hidden = !row.textContent.toLocaleLowerCase().includes(term);
        });
      });
    });

    document.querySelectorAll('table.table-dtc').forEach(initTableTools);
  }

  document.addEventListener('DOMContentLoaded', documentReady);
})();
