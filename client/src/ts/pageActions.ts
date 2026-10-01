type MessageBody = { readonly message: string }

const isMessageBody = (value: unknown): value is MessageBody =>
  typeof (value as { message?: unknown } | null | undefined)?.message === 'string'

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
  button.disabled = true
  const response = await fetch(container.dataset.endpoint ?? '', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      'X-SecurityID':
        container.querySelector<HTMLInputElement>('input[name="SecurityID"]')?.value ?? '',
    },
    body: JSON.stringify({ page_id: button.dataset.pageId, action: button.dataset.action }),
  }).catch(() => undefined)

  if (response?.ok) {
    reload()
  } else {
    // A network failure leaves no response, a non-JSON error page no body.
    const body: unknown = await response?.json().catch(() => undefined)
    showError(
      container,
      isMessageBody(body) ? body.message : (container.dataset.errorMessage ?? ''),
    )
  }
  button.disabled = false
}

function showError(container: HTMLElement, message: string): void {
  const target = container.querySelector<HTMLElement>('[data-action-message]')
  if (target) {
    target.textContent = message
    target.dataset.state = 'error'
    target.classList.remove('ssat:hidden')
  }
}
