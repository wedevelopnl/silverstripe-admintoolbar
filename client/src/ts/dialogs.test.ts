import { byHook } from './testing/dom'
import { initDialogs } from './dialogs'

const TOOLBAR = `
  <div id="admin-toolbar" data-admin-toolbar>
    <div data-dialog-anchor>
      <dialog id="toggles"><ul data-dialog-content><li>Toggle</li></ul></dialog>
      <button type="button" data-toggle-dialog="toggles">Toggles</button>
    </div>
    <button type="button" data-toggle-dialog="PageMenu"><span data-trigger-icon></span></button>
    <dialog id="PageMenu">
      <div data-page-content>
        <button type="button" data-toggle-dialog="PageMenu" data-close>Close</button>
      </div>
    </dialog>
    <button type="button" data-toggle-dialog="missing" data-missing-trigger>Missing</button>
    <div id="not-a-dialog"></div>
    <button type="button" data-toggle-dialog="not-a-dialog" data-wrong-trigger>Wrong</button>
  </div>
  <dialog id="host-dialog" open></dialog>
  <button type="button" data-toggle-dialog="PageMenu" data-host-trigger>Host</button>`

const dialog = (id: string) => document.getElementById(id) as HTMLDialogElement

function click(element: Element): MouseEvent {
  const event = new MouseEvent('click', { bubbles: true, cancelable: true })
  element.dispatchEvent(event)
  return event
}

describe('initDialogs', () => {
  beforeEach(() => {
    document.body.innerHTML = TOOLBAR
    initDialogs()
  })

  it('opens the matching dialog, and closes it on the next click', () => {
    click(byHook('data-trigger-icon'))

    expect(dialog('PageMenu').showModal).toHaveBeenCalledTimes(1)
    expect(dialog('PageMenu').open).toBe(true)

    click(byHook('data-close'))

    expect(dialog('PageMenu').open).toBe(false)
  })

  it('prevents the trigger default action', () => {
    expect(click(byHook('data-trigger-icon')).defaultPrevented).toBe(true)
  })

  it('ignores a trigger for an unknown id', () => {
    expect(() => click(byHook('data-missing-trigger'))).not.toThrow()
    expect(HTMLDialogElement.prototype.showModal).not.toHaveBeenCalled()
  })

  it('ignores a trigger whose target is not a dialog', () => {
    expect(() => click(byHook('data-wrong-trigger'))).not.toThrow()
    expect(HTMLDialogElement.prototype.showModal).not.toHaveBeenCalled()
  })

  it('ignores triggers outside the toolbar', () => {
    const event = click(byHook('data-host-trigger'))

    expect(dialog('PageMenu').open).toBe(false)
    expect(event.defaultPrevented).toBe(false)
  })

  it('closes an open dialog when its backdrop is clicked', () => {
    click(byHook('data-trigger-icon'))

    click(dialog('PageMenu'))

    expect(dialog('PageMenu').open).toBe(false)
  })

  it('keeps the dialog open when its content is clicked', () => {
    click(byHook('data-trigger-icon'))

    click(byHook('data-page-content'))

    expect(dialog('PageMenu').open).toBe(true)
  })

  it('leaves dialogs outside the toolbar alone', () => {
    click(dialog('host-dialog'))

    expect(dialog('host-dialog').open).toBe(true)
  })

  it('positions an anchored dialog at its anchor before opening', () => {
    vi.spyOn(byHook('data-dialog-anchor'), 'getBoundingClientRect').mockReturnValue({
      top: 700,
      left: 40,
    } as DOMRect)
    let positionAtOpen = ''
    vi.mocked(HTMLDialogElement.prototype.showModal).mockImplementation(function showModal(
      this: HTMLDialogElement,
    ) {
      positionAtOpen = `${this.style.top} ${this.style.left}`
      this.setAttribute('open', '')
    })

    click(document.querySelector('[data-toggle-dialog="toggles"]') as Element)

    expect(positionAtOpen).toBe('700px 40px')
  })

  it('does not position a dialog outside an anchor', () => {
    click(byHook('data-trigger-icon'))

    expect(dialog('PageMenu').style.top).toBe('')
  })
})
