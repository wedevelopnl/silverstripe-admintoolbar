import { byHook } from './testing/dom'
import { initSettingToggle, isSettingEnabled, type SettingHook } from './settingToggle'

const HOOKS: SettingHook[] = ['data-queries-toggle', 'data-timing-toggle']

describe.each(HOOKS)('initSettingToggle(%s)', (hook) => {
  const key = `[${hook}]`
  const checkbox = () => byHook<HTMLInputElement>(hook)
  let reload: ReturnType<typeof vi.fn<() => void>>

  beforeEach(() => {
    document.body.innerHTML = `<label><input type="checkbox" ${hook}>Setting</label>`
    reload = vi.fn<() => void>()
  })

  it('is unchecked when nothing is stored', () => {
    initSettingToggle(hook, document, reload)

    expect(checkbox().checked).toBe(false)
  })

  it('is checked when the 2.x key stores true', () => {
    localStorage.setItem(key, 'true')

    initSettingToggle(hook, document, reload)

    expect(checkbox().checked).toBe(true)
  })

  it('stores the new state and reloads on change', () => {
    initSettingToggle(hook, document, reload)

    checkbox().click()

    expect(localStorage.getItem(key)).toBe('true')
    expect(reload).toHaveBeenCalledTimes(1)

    checkbox().click()

    expect(localStorage.getItem(key)).toBe('false')
    expect(reload).toHaveBeenCalledTimes(2)
  })

  it('does nothing without its checkbox', () => {
    document.body.innerHTML = ''

    expect(() => initSettingToggle(hook, document, reload)).not.toThrow()
  })

  it('is enabled only for the stored value true', () => {
    expect(isSettingEnabled(hook)).toBe(false)

    localStorage.setItem(key, 'false')
    expect(isSettingEnabled(hook)).toBe(false)

    localStorage.setItem(key, 'true')
    expect(isSettingEnabled(hook)).toBe(true)
  })

  it('binds within the whole document by default', () => {
    localStorage.setItem(key, 'true')

    initSettingToggle(hook)

    expect(checkbox().checked).toBe(true)
  })
})
