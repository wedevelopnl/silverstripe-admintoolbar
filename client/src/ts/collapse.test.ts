import { byHook } from './testing/dom'
import { COLLAPSE_KEY, initCollapse } from './collapse'

const TOOLBAR = `
  <div id="admin-toolbar" data-admin-toolbar>
    <div id="admin-toolbar-panel" data-toolbar-panel class="ssat:hidden ssat:flex"></div>
    <button type="button" data-toggle-admin-toolbar aria-controls="admin-toolbar-panel" aria-expanded="false"></button>
  </div>`

const panel = () => byHook('data-toolbar-panel')
const button = () => byHook<HTMLButtonElement>('data-toggle-admin-toolbar')

describe('initCollapse', () => {
  beforeEach(() => {
    document.body.innerHTML = TOOLBAR
  })

  it('shows the panel when nothing is stored', () => {
    initCollapse()

    expect(panel().classList.contains('ssat:hidden')).toBe(false)
    expect(button().getAttribute('aria-expanded')).toBe('true')
  })

  it('keeps the panel hidden when it was collapsed', () => {
    localStorage.setItem(COLLAPSE_KEY, 'false')

    initCollapse()

    expect(panel().classList.contains('ssat:hidden')).toBe(true)
    expect(button().getAttribute('aria-expanded')).toBe('false')
  })

  it('keeps the 2.x storage key', () => {
    expect(COLLAPSE_KEY).toBe('ss-at-admin-toolbar-active')
  })

  it('toggles both ways on click and stores the state', () => {
    initCollapse()

    button().click()

    expect(panel().classList.contains('ssat:hidden')).toBe(true)
    expect(button().getAttribute('aria-expanded')).toBe('false')
    expect(localStorage.getItem(COLLAPSE_KEY)).toBe('false')

    button().click()

    expect(panel().classList.contains('ssat:hidden')).toBe(false)
    expect(button().getAttribute('aria-expanded')).toBe('true')
    expect(localStorage.getItem(COLLAPSE_KEY)).toBe('true')
  })

  it('renders expanded and still toggles when storage throws', () => {
    vi.spyOn(localStorage, 'getItem').mockImplementation(() => {
      throw new DOMException('denied', 'SecurityError')
    })
    vi.spyOn(localStorage, 'setItem').mockImplementation(() => {
      throw new DOMException('denied', 'SecurityError')
    })

    initCollapse()

    expect(panel().classList.contains('ssat:hidden')).toBe(false)

    button().click()

    expect(panel().classList.contains('ssat:hidden')).toBe(true)
    expect(button().getAttribute('aria-expanded')).toBe('false')
  })

  it('does nothing without a panel', () => {
    panel().remove()

    expect(() => initCollapse()).not.toThrow()
    expect(button().getAttribute('aria-expanded')).toBe('false')
  })

  it('does nothing without a button', () => {
    button().remove()

    expect(() => initCollapse()).not.toThrow()
    expect(panel().classList.contains('ssat:hidden')).toBe(true)
  })
})
