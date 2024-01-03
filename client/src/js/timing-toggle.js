const selector = '[data-timing-toggle]';
const checkbox = document.querySelector(selector);

function getState() {
  return localStorage.getItem(selector) === 'true';
}

function setState(state) {
  localStorage.setItem(selector, state);
}

if (checkbox && checkbox.checked) {
  getState();
}

checkbox?.addEventListener('change', (e) => {
  setState(e.currentTarget.checked);
});
