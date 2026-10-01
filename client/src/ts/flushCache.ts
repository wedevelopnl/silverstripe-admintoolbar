export function initFlushCache(
  root: ParentNode = document,
  location: Location = window.location,
  reload: () => void = () => window.location.reload(),
): void {
  const button = root.querySelector<HTMLButtonElement>('[data-flush-cache-button]')
  button?.addEventListener('click', () => {
    void flush(button, location, reload)
  })
}

async function flush(
  button: HTMLButtonElement,
  location: Location,
  reload: () => void,
): Promise<void> {
  const url = new URL(location.href)
  url.searchParams.set('flush', '1')
  url.searchParams.set('AdminToolbarDisabled', '1')

  button.disabled = true
  button.setAttribute('aria-busy', 'true')
  try {
    await fetch(url, { credentials: 'same-origin' })
    reload()
  } catch {
    button.disabled = false
    button.removeAttribute('aria-busy')
  }
}
