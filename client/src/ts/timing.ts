import { format } from './format'
import { isSettingEnabled } from './settingToggle'

/** Times a full load of the current page (toolbar disabled) in a hidden iframe. */
export function initTiming(
  root: ParentNode = document,
  location: Location = window.location,
  now: () => number = () => performance.now(),
): void {
  const button = root.querySelector<HTMLButtonElement>('[data-timing-button]')
  if (!button || !isSettingEnabled('data-timing-toggle')) {
    return
  }
  button.classList.remove('ssat:hidden')

  const url = new URL(location.href)
  url.searchParams.set('AdminToolbarDisabled', '1')

  const iframe = document.createElement('iframe')
  iframe.className = 'ssat:hidden'
  iframe.setAttribute('aria-hidden', 'true')
  iframe.tabIndex = -1

  const start = now()
  iframe.addEventListener(
    'load',
    () => {
      const label = button.querySelector('[data-button-label]')
      if (label) {
        label.textContent = format(button.dataset.summary ?? '', { ms: Math.round(now() - start) })
      }
      iframe.remove()
    },
    { once: true },
  )
  iframe.src = url.toString()
  document.body.append(iframe)
}
