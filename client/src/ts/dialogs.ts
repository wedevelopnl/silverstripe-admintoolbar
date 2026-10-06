/**
 * Toggles `dialog#<id>` from `[data-toggle-dialog="<id>"]` triggers, and closes a dialog
 * when its backdrop (the dialog element itself) is clicked. Listening on the toolbar
 * root keeps host-site dialogs and triggers out of reach.
 */
export function initDialogs(doc: Document = document): void {
  doc.getElementById('admin-toolbar')?.addEventListener('click', (event) => {
    const target = event.target
    if (!(target instanceof Element)) {
      return
    }

    const trigger = target.closest<HTMLElement>('[data-toggle-dialog]')
    if (trigger) {
      event.preventDefault()
      // Stryker disable next-line StringLiteral: unreachable — closest('[data-toggle-dialog]') matched, so the attribute exists
      toggle(doc, trigger.dataset.toggleDialog ?? '')
    } else if (target instanceof HTMLDialogElement && target.open) {
      target.close()
    }
  })
}

function toggle(doc: Document, id: string): void {
  const dialog = doc.getElementById(id)
  if (!(dialog instanceof HTMLDialogElement)) {
    return
  }

  if (dialog.open) {
    dialog.close()
    return
  }

  const anchor = dialog.closest<HTMLElement>('[data-dialog-anchor]')
  if (anchor) {
    const { top, left } = anchor.getBoundingClientRect()
    dialog.style.top = `${top}px`
    dialog.style.left = `${left}px`
  }
  dialog.showModal()
  // showModal() focuses the first control, and Safari (which never focuses a clicked button)
  // rings it as :focus-visible. Focus the dialog itself; the first Tab reaches that control.
  dialog.tabIndex = -1
  dialog.focus()
}
