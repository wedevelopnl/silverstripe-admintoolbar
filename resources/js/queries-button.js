(() => {
  const configKey = '[data-queries-toggle]';
  const button = document.querySelector('[data-queries-button]');

  const isToggled = localStorage.getItem(configKey) === 'true';

  if (!button) {
    return false;
  }

  if (isToggled) {
    button.classList.remove('admin-toolbar-hidden');
  }

  const queriesURL = new URL(window.location.href);

  queriesURL.searchParams.set('AdminToolbarDisabled', 1);
  queriesURL.searchParams.set('showqueries', 'inline');

  function parseResponse(text) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(text, 'text/html');
    const timeRegex = new RegExp('(\\d\\.\\d*)s');
    const timings = [];

    doc.querySelectorAll('p.alert.alert-warning').forEach((el) => {
      const matches = timeRegex.exec(el.innerHTML);
      timings.push(parseFloat(matches[1], 10));
    });

    const queryTimeMS = timings.length ? (timings.reduce((prev, cur) => {
      return prev + cur;
    }, 0) * 1000).toFixed(0) : 0;

    button.innerHTML = queryTimeMS + 'ms (' + timings.length + ' queries)';
  }

  fetch(queriesURL.toString())
    .then(res => {
      return res.text();
    })
    .then(parseResponse);


})();
