import './dialog';

document.addEventListener('DOMContentLoaded', () => {
  const adminToolbar = document.getElementById('admin-toolbar');
  const adminToolbarToggle = document.querySelector('[data-toggle-admin-toolbar]');
  const initialAdminToolbarActive = localStorage.getItem('ss-at-admin-toolbar-active');
  const inactiveClass = 'ss-at-collapse';

  if (initialAdminToolbarActive !== 'false') {
    adminToolbar.querySelector('.admin-toolbar-inner').classList.remove(inactiveClass);
  } else {
    adminToolbarToggle.classList.toggle('ss-at-origin-center');
    adminToolbarToggle.classList.toggle('ss-at-rotate-180');
  }

  adminToolbarToggle.addEventListener('click', () => {
    const toolbar = adminToolbar.querySelector('.admin-toolbar-inner');
    toolbar.classList.toggle(inactiveClass);

    adminToolbarToggle.classList.toggle('ss-at-origin-center');
    adminToolbarToggle.classList.toggle('ss-at-rotate-180');

    localStorage.setItem('ss-at-admin-toolbar-active', !toolbar.classList.contains(inactiveClass));
  });

  const csrfToken = document.querySelector('.admin-toolbar-menu #SecurityID');

  const url = '/admintoolbaraction/pageAction';

  document.querySelectorAll('a[data-action]').forEach((element) => {
    element.addEventListener('click', (e) => {
      e.preventDefault();
      const { pageid, action } = e.target.dataset;

      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Securityid': csrfToken.value,
        },
        body: JSON.stringify({
          page_id: pageid,
          action,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          const responseMessageContainer = document.getElementById('response-message');
          if (responseMessageContainer && data.message) {
            const messageSpan = responseMessageContainer.querySelector('span');
            if (messageSpan) {
              messageSpan.textContent = data.message;
              responseMessageContainer.classList.remove('ss-at-hidden'); // Toon het message container
            }
          }
        })
        .catch((error) => {
          console.error('Error:', error);
          // Geef hier feedback over de fout aan de gebruiker
        });
    });
  });
});
