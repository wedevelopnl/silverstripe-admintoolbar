type MessageBody = { readonly message: string }

const isMessageBody = (value: unknown): value is MessageBody =>
  typeof value === 'object' &&
  value !== null &&
  typeof (value as { message?: unknown }).message === 'string'

export function initPageActions(
  root: ParentNode = document,
  reload: () => void = () => window.location.reload(),
): void {
  for (const container of root.querySelectorAll<HTMLElement>('[data-page-actions]')) {
    container.addEventListener('click', (event) => {
      const button =
        event.target instanceof Element ? event.target.closest('button[data-action]') : null
      if (button instanceof HTMLButtonElement && container.contains(button)) {
        event.preventDefault()
        void run(container, button, reload)
      }
    })
  }
}

async function run(
  container: HTMLElement,
  button: HTMLButtonElement,
  reload: () => void,
): Promise<void> {
  const fallback = container.dataset.errorMessage ?? ''
  button.disabled = true
  try {
    const response = await fetch(container.dataset.endpoint ?? '', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-SecurityID':
          container.querySelector<HTMLInputElement>('input[name="SecurityID"]')?.value ?? '',
      },
      body: JSON.stringify({ page_id: button.dataset.pageId, action: button.dataset.action }),
    })
    if (response.ok) {
      reload()
      return
    }
    const body: unknown = await response.json().catch(() => null)
    showError(container, isMessageBody(body) ? body.message : fallback)
  } catch {
    showError(container, fallback)
  } finally {
    button.disabled = false
  }
}

function showError(container: HTMLElement, message: string): void {
  const target = container.querySelector<HTMLElement>('[data-action-message]')
  if (target) {
    target.textContent = message
    target.dataset.state = 'error'
    target.classList.remove('ssat:hidden')
  }
}
