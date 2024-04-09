const configKey = '[data-queries-toggle]';
const isToggled = localStorage.getItem(configKey) === 'true';
const button = document.querySelector('[data-queries-button]');
const queriesURL = new URL(window.location.href);
const dialog = document.createElement('dialog');

(() => {
  if (!isToggled) {
    return;
  }

  button.classList.remove('ss-at-hidden');
  dialog.classList.add('admin-toolbar-queries-button');

  queriesURL.searchParams.set('AdminToolbarDisabled', '1');
  queriesURL.searchParams.set('showqueries', 'inline');

  function parseResponse(text) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(text, 'text/html');
    const timeRegex = /(\d+\.\d+)s/;
    const timings = [];
    const queries = [];

    doc.querySelectorAll('p.alert.alert-warning').forEach((el) => {
      const matches = timeRegex.exec(el.innerHTML);
      queries.push(el.innerHTML);
      timings.push(parseFloat(matches[1], 10));
    });

    const queryTimeMS = timings.length
      ? (timings.reduce((prev, cur) => prev + cur, 0) * 1000).toFixed(0)
      : 0;

    button.innerHTML = `${queryTimeMS}ms (${timings.length} queries)`;
    dialog.innerHTML = `<ul>${queries.reduce((acc, item) => `${acc}<li><code>${item}</code></li>`, '')}</ul>`;

    button.addEventListener('click', () => {
      dialog.showModal();
    });

    dialog.addEventListener('click', (e) => {
      const rect = dialog.getBoundingClientRect();

      if ((e.clientY < rect.top || e.clientY > rect.bottom) || (e.clientX < rect.left || e.clientX > rect.right)) {
        dialog.close();
      }
    });

    document.body.appendChild(dialog);
  }

  fetch(queriesURL.toString())
    .then((res) => res.text())
    .then(parseResponse);
})();
