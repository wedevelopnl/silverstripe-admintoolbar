import { readItem, writeItem } from './storage'

/** The 2.x key, so members' choices survive the upgrade; `'false'` means collapsed. */
export const COLLAPSE_KEY = 'ss-at-admin-toolbar-active'

export function initCollapse(root: ParentNode = document): void {
  const panel = root.querySelector<HTMLElement>('[data-toolbar-panel]')
  const button = root.querySelector<HTMLElement>('[data-toggle-admin-toolbar]')
  if (!panel || !button) {
    return
  }

  const show = (expanded: boolean): void => {
    panel.classList.toggle('ssat:hidden', !expanded)
    button.setAttribute('aria-expanded', String(expanded))
  }

  show(readItem(COLLAPSE_KEY) !== 'false')

  button.addEventListener('click', () => {
    const expanded = panel.classList.contains('ssat:hidden')
    show(expanded)
    writeItem(COLLAPSE_KEY, String(expanded))
  })
}
