(() => {
  const selector = '[data-flush-cache-button]';
  const button = document.querySelector(selector);

  if (!button) {
    return;
  }

  button.addEventListener('click', () => {
      const flushURL = new URL(window.location.href);

      flushURL.searchParams.set('AdminToolbarDisabled', '1');
      flushURL.searchParams.set('flush', '1');

      button.classList.remove('font-icon-back-in-time');
      button.classList.add('font-icon-spinner');

      fetch(flushURL.toString())
          .then(res => {
              window.location.reload();
          });
  });
})();
