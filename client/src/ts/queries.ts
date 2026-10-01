import { format } from './format'
import { isSettingEnabled } from './settingToggle'

export type Query = { readonly sql: string; readonly seconds: number }

/** `Database::displayQuery()` ends each query with its duration, e.g. `0.0012s`. */
const DURATION = /(\d+(?:\.\d+)?)s$/

/** Reads the queries Silverstripe prints for `?showqueries=inline`. */
export function parseQueries(html: string): Query[] {
  const page = new DOMParser().parseFromString(html, 'text/html')

  return [...page.querySelectorAll('p.alert.alert-warning')].map((element) => {
    const sql = element.textContent.trim()
    const duration = DURATION.exec(sql)?.[1]

    return { sql, seconds: duration === undefined ? 0 : Number.parseFloat(duration) }
  })
}

export async function initQueries(
  root: ParentNode = document,
  location: Location = window.location,
): Promise<void> {
  const button = root.querySelector<HTMLButtonElement>('[data-queries-button]')
  if (!button || !isSettingEnabled('data-queries-toggle')) {
    return
  }
  button.classList.remove('ssat:hidden')

  const url = new URL(location.href)
  url.searchParams.set('showqueries', 'inline')
  url.searchParams.set('AdminToolbarDisabled', '1')

  let queries: Query[]
  try {
    const response = await fetch(url, { credentials: 'same-origin' })
    if (!response.ok) {
      return
    }
    queries = parseQueries(await response.text())
  } catch {
    return
  }

  const label = button.querySelector('[data-button-label]')
  const dialog = createDialog(queries, label?.textContent ?? '')
  const seconds = queries.reduce((sum, query) => sum + query.seconds, 0)
  if (label) {
    label.textContent = format(button.dataset.summary ?? '', {
      ms: Math.round(seconds * 1000),
      count: queries.length,
    })
  }

  ;(button.closest('#admin-toolbar') ?? document.body).append(dialog)
  button.addEventListener('click', () => {
    dialog.showModal()
  })
}

function createDialog(queries: readonly Query[], title: string): HTMLDialogElement {
  const dialog = document.createElement('dialog')
  dialog.className = 'ssat:w-6/12 ssat:bg-white ssat:p-6 ssat:rounded-lg ssat:backdrop:bg-black/50'
  dialog.setAttribute('aria-label', title)

  const list = document.createElement('ul')
  for (const query of queries) {
    const item = document.createElement('li')
    const code = document.createElement('code')
    code.textContent = query.sql
    item.append(code)
    list.append(item)
  }
  dialog.append(list)

  return dialog
}
