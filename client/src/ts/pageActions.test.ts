import { byHook, jsonResponse, textResponse } from './testing/dom'
import { initPageActions } from './pageActions'

const ENDPOINT = '/sub/admintoolbaraction/pageAction'
const FALLBACK = 'The action could not be completed.'

const actions = (id: string) => `
  <div data-page-actions data-container="${id}" data-endpoint="${ENDPOINT}" data-error-message="${FALLBACK}">
    <input type="hidden" name="SecurityID" value="token-${id}">
    <p data-action-message class="ssat:hidden" role="alert"></p>
    <ul>
      <li><button type="button" data-page-id="7" data-action="unpublish"><span data-icon></span>Unpublish</button></li>
    </ul>
    <p data-outside-button>Last edited</p>
  </div>`

const container = (id: string) => document.querySelector(`[data-container="${id}"]`) as HTMLElement
const actionButton = (id: string) =>
  container(id).querySelector('button[data-action]') as HTMLButtonElement
const message = (id: string) => container(id).querySelector('[data-action-message]') as HTMLElement

describe('initPageActions', () => {
  let reload: ReturnType<typeof vi.fn<() => void>>
  let fetchMock: ReturnType<typeof vi.fn<typeof fetch>>

  beforeEach(() => {
    document.body.innerHTML = actions('a')
    reload = vi.fn<() => void>()
    fetchMock = vi.fn<typeof fetch>()
    vi.stubGlobal('fetch', fetchMock)
    initPageActions(document, reload)
  })

  async function settle(id = 'a'): Promise<void> {
    await vi.waitFor(() => expect(actionButton(id).disabled).toBe(false))
  }

  it('posts the page id and action with the token', async () => {
    fetchMock.mockResolvedValue(jsonResponse(200, { message: 'Page unpublished' }))

    actionButton('a').click()
    await settle()

    expect(fetchMock).toHaveBeenCalledTimes(1)
    const [url, init] = fetchMock.mock.calls[0] ?? []
    expect(url).toBe(ENDPOINT)
    expect(init?.method).toBe('POST')
    expect(init?.credentials).toBe('same-origin')
    expect(init?.headers).toMatchObject({
      'Content-Type': 'application/json',
      'X-SecurityID': 'token-a',
    })
    expect(JSON.parse(String(init?.body))).toEqual({ page_id: '7', action: 'unpublish' })
  })

  it('reloads on success without showing a message', async () => {
    fetchMock.mockResolvedValue(jsonResponse(200, { message: 'Page unpublished' }))

    actionButton('a').click()
    await settle()

    expect(reload).toHaveBeenCalledTimes(1)
    expect(message('a').classList.contains('ssat:hidden')).toBe(true)
  })

  it.each([403, 409])('shows the server message on %i', async (status) => {
    fetchMock.mockResolvedValue(jsonResponse(status, { message: `Refused with ${status}` }))

    actionButton('a').click()
    await settle()

    expect(message('a').textContent).toBe(`Refused with ${status}`)
    expect(message('a').classList.contains('ssat:hidden')).toBe(false)
    expect(message('a').dataset.state).toBe('error')
    expect(reload).not.toHaveBeenCalled()
  })

  it('shows the fallback message when the error body is not JSON', async () => {
    fetchMock.mockResolvedValue(textResponse(500, '<h1>Server error</h1>'))

    actionButton('a').click()
    await settle()

    expect(message('a').textContent).toBe(FALLBACK)
    expect(reload).not.toHaveBeenCalled()
  })

  it('shows the fallback message when the error body has no message', async () => {
    fetchMock.mockResolvedValue(jsonResponse(400, { error: 'nope' }))

    actionButton('a').click()
    await settle()

    expect(message('a').textContent).toBe(FALLBACK)
  })

  it('shows the fallback message and re-enables the button on a network error', async () => {
    fetchMock.mockRejectedValue(new TypeError('Failed to fetch'))

    actionButton('a').click()
    await settle()

    expect(message('a').textContent).toBe(FALLBACK)
    expect(message('a').classList.contains('ssat:hidden')).toBe(false)
    expect(reload).not.toHaveBeenCalled()
  })

  it('shows an empty message when an overridden template renders no fallback', async () => {
    delete container('a').dataset.errorMessage
    fetchMock.mockRejectedValue(new TypeError('Failed to fetch'))

    actionButton('a').click()
    await settle()

    expect(message('a').textContent).toBe('')
    expect(message('a').classList.contains('ssat:hidden')).toBe(false)
  })

  it('posts to the current page when an overridden template renders no endpoint', async () => {
    delete container('a').dataset.endpoint
    fetchMock.mockResolvedValue(jsonResponse(200, {}))

    actionButton('a').click()
    await settle()

    expect(fetchMock.mock.calls[0]?.[0]).toBe('')
  })

  it('prevents the default action of the clicked button', () => {
    fetchMock.mockReturnValue(new Promise<Response>(() => undefined))
    const event = new MouseEvent('click', { bubbles: true, cancelable: true })

    actionButton('a').dispatchEvent(event)

    expect(event.defaultPrevented).toBe(true)
  })

  it('disables the button while the request is pending', () => {
    fetchMock.mockReturnValue(new Promise<Response>(() => undefined))

    actionButton('a').click()

    expect(actionButton('a').disabled).toBe(true)
  })

  it('handles a click on the icon inside the button', async () => {
    fetchMock.mockResolvedValue(jsonResponse(200, {}))

    byHook('data-icon').click()
    await settle()

    expect(fetchMock).toHaveBeenCalledTimes(1)
  })

  it('ignores clicks outside an action button', () => {
    byHook('data-outside-button').click()

    expect(fetchMock).not.toHaveBeenCalled()
  })

  it('keeps two containers independent', async () => {
    document.body.innerHTML = actions('a') + actions('b')
    initPageActions(document, reload)
    fetchMock.mockResolvedValue(jsonResponse(403, { message: 'Refused' }))

    actionButton('b').click()
    await settle('b')

    expect(fetchMock.mock.calls[0]?.[1]?.headers).toMatchObject({ 'X-SecurityID': 'token-b' })
    expect(message('b').textContent).toBe('Refused')
    expect(message('a').classList.contains('ssat:hidden')).toBe(true)
    expect(fetchMock).toHaveBeenCalledTimes(1)
  })

  it('binds within the whole document by default', async () => {
    document.body.innerHTML = actions('a')
    initPageActions()
    fetchMock.mockResolvedValue(jsonResponse(404, { message: 'Gone' }))

    actionButton('a').click()
    await settle()

    expect(message('a').textContent).toBe('Gone')
  })

  it('reloads the page the browser is on by default', async () => {
    const browserReload = vi.fn<() => void>()
    vi.stubGlobal('location', { ...window.location, reload: browserReload })
    document.body.innerHTML = actions('a')
    initPageActions()
    fetchMock.mockResolvedValue(jsonResponse(200, { message: 'Page unpublished' }))

    actionButton('a').click()
    await settle()

    expect(browserReload).toHaveBeenCalledTimes(1)
  })

  it('sends an empty token when an overridden template renders none', async () => {
    container('a').querySelector('input[name="SecurityID"]')?.remove()
    fetchMock.mockResolvedValue(jsonResponse(400, { message: 'Expired' }))

    actionButton('a').click()
    await settle()

    expect(fetchMock.mock.calls[0]?.[1]?.headers).toMatchObject({ 'X-SecurityID': '' })
  })

  it('re-enables the button when an overridden template has no message element', async () => {
    message('a').remove()
    fetchMock.mockResolvedValue(jsonResponse(403, { message: 'Refused' }))

    actionButton('a').click()
    await settle()

    expect(reload).not.toHaveBeenCalled()
  })
})
