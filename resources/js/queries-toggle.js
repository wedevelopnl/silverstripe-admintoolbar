(() => {
  const selector = '[data-queries-toggle]';
  const checkbox = document.querySelector(selector);

  if (!checkbox) {
    return;
  }

  function getState() {
    return localStorage.getItem(selector) === 'true';
  }

  function setState(state) {
    localStorage.setItem(selector, state);
  }

  checkbox.checked = getState();

  checkbox.addEventListener('change', (e) => {
    setState(e.currentTarget.checked);
  });
})();
