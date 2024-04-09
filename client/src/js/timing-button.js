const configKey = '[data-timing-toggle]';
const button = document.querySelector('[data-timing-button]');
const isToggled = localStorage.getItem(configKey) === 'true';
const iframe = document.createElement('iframe');
const startTime = new Date().getTime();
const adminDisabledURL = new URL(window.location.href);

(() => {
  if (!isToggled) {
    return;
  }

  button.classList.remove('ss-at-hidden');
  adminDisabledURL.searchParams.set('AdminToolbarDisabled', '1');

  iframe.addEventListener('load', () => {
    button.querySelector('.ss-at-btn-content').innerHTML = `${new Date().getTime() - startTime}ms`;
    iframe.remove();
  });

  iframe.src = adminDisabledURL.toString();
  iframe.style.display = 'none';

  document.body.appendChild(iframe);
})();
