import { byHook, fakeLocation } from './testing/dom'
import { initTiming } from './timing'

const TOOLBAR = `
  <div id="admin-toolbar" data-admin-toolbar>
    <button type="button" data-timing-button data-summary="{ms} ms" class="ssat:btn ssat:hidden">
      <span data-button-label>Timing</span>
    </button>
  </div>`

const button = () => byHook<HTMLButtonElement>('data-timing-button')

describe('initTiming', () => {
  beforeEach(() => {
    document.body.innerHTML = TOOLBAR
  })

  it('does nothing while the setting is off', () => {
    initTiming(document, fakeLocation(), () => 0)

    expect(document.querySelector('iframe')).toBeNull()
    expect(button().classList.contains('ssat:hidden')).toBe(true)
  })

  it('does nothing without a button', () => {
    localStorage.setItem('[data-timing-toggle]', 'true')
    document.body.innerHTML = ''

    initTiming(document, fakeLocation(), () => 0)

    expect(document.querySelector('iframe')).toBeNull()
  })

  it('times a hidden load of the current page', () => {
    localStorage.setItem('[data-timing-toggle]', 'true')
    const now = vi.fn<() => number>().mockReturnValueOnce(1000).mockReturnValueOnce(1234.6)

    initTiming(document, fakeLocation('https://example.com/about/?stage=Live'), now)

    expect(button().classList.contains('ssat:hidden')).toBe(false)
    const iframe = document.querySelector('iframe') as HTMLIFrameElement
    expect(iframe.classList.contains('ssat:hidden')).toBe(true)
    const url = new URL(iframe.src)
    expect(url.pathname).toBe('/about/')
    expect(url.searchParams.get('stage')).toBe('Live')
    expect(url.searchParams.get('AdminToolbarDisabled')).toBe('1')

    iframe.dispatchEvent(new Event('load'))

    expect(byHook('data-button-label').textContent).toBe('235 ms')
    expect(document.querySelector('iframe')).toBeNull()
  })

  it('times the page the browser is on by default', () => {
    localStorage.setItem('[data-timing-toggle]', 'true')

    initTiming()
    const iframe = document.querySelector('iframe') as HTMLIFrameElement
    iframe.dispatchEvent(new Event('load'))

    expect(new URL(iframe.src).origin).toBe(window.location.origin)
    expect(byHook('data-button-label').textContent).toMatch(/^\d+ ms$/)
  })

  it('cleans up the iframe when an overridden template has no label element', () => {
    localStorage.setItem('[data-timing-toggle]', 'true')
    byHook('data-button-label').remove()

    initTiming(document, fakeLocation(), () => 0)
    document.querySelector('iframe')?.dispatchEvent(new Event('load'))

    expect(document.querySelector('iframe')).toBeNull()
  })
})
