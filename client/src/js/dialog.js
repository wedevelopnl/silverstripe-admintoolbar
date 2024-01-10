const dialogs = document.querySelectorAll('dialog');

dialogs.forEach((dialog) => {
  const toggleButtons = document.querySelectorAll(`[data-toggle-dialog="${dialog.id}"]`);

  toggleButtons.forEach((toggleButton) => {
    toggleButton.addEventListener('click', (e) => {
      e.preventDefault();
      if (dialog.open) {
        dialog.close();
      } else {
        dialog.showModal();
      }
    });
  });
});

window.addEventListener('click', (e) => {
  if (e.target.nodeName === 'DIALOG') {
    e.target.close();
  }
});
