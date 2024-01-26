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
});
