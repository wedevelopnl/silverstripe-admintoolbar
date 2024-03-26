const selector = '[data-queries-toggle]';
const checkbox = document.querySelector(selector);

function getState() {
  return localStorage.getItem(selector) === 'true';
}

function setState(state) {
  localStorage.setItem(selector, state);
}

document.addEventListener('DOMContentLoaded', () => {
  if (checkbox) {
    checkbox.checked = getState();
  }
});

checkbox?.addEventListener('change', (e) => {
  setState(e.currentTarget.checked);
  window.location.reload();
});
