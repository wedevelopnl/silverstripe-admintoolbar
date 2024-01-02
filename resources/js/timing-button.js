(() => {
  const button = document.querySelector('[data-timing-button]');

  if (!button) {
    return false;
  }

  const iframe = document.createElement('iframe');
  const startTime = new Date().getTime();
  const adminDisabledURL = new URL(window.location.href);

  adminDisabledURL.searchParams.set('AdminToolbarDisabled', 1);

  iframe.addEventListener('load', () => {
    button.innerHTML = new Date().getTime() - startTime + 'ms';
  });

  iframe.src = adminDisabledURL.toString();
  iframe.style.display = 'none';

  document.body.appendChild(iframe);
})();
