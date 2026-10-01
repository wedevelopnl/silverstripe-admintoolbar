import { byHook, fakeLocation, PAGE_URL, requestedUrl, textResponse } from './testing/dom'
import { initFlushCache } from './flushCache'

const button = () => byHook<HTMLButtonElement>('data-flush-cache-button')

describe('initFlushCache', () => {
  let reload: ReturnType<typeof vi.fn<() => void>>
  let fetchMock: ReturnType<typeof vi.fn<typeof fetch>>

  beforeEach(() => {
    document.body.innerHTML = '<button type="button" data-flush-cache-button>Flush</button>'
    reload = vi.fn<() => void>()
    fetchMock = vi.fn<typeof fetch>()
    vi.stubGlobal('fetch', fetchMock)
    initFlushCache(document, fakeLocation('https://example.com/about/?stage=Live'), reload)
  })

  it('fetches the current page with flush and the toolbar disabled, then reloads', async () => {
    fetchMock.mockResolvedValue(textResponse(200, ''))

    button().click()

    expect(button().getAttribute('aria-busy')).toBe('true')
    expect(button().disabled).toBe(true)
    await vi.waitFor(() => expect(reload).toHaveBeenCalledTimes(1))
    const url = requestedUrl(fetchMock)
    expect(url.pathname).toBe('/about/')
    expect(url.searchParams.get('stage')).toBe('Live')
    expect(url.searchParams.get('flush')).toBe('1')
    expect(url.searchParams.get('AdminToolbarDisabled')).toBe('1')
    expect(fetchMock.mock.calls[0]?.[1]).toMatchObject({ credentials: 'same-origin' })
  })

  it('re-enables the button without reloading when the request fails', async () => {
    fetchMock.mockRejectedValue(new TypeError('Failed to fetch'))

    button().click()

    await vi.waitFor(() => expect(button().disabled).toBe(false))
    expect(button().hasAttribute('aria-busy')).toBe(false)
    expect(reload).not.toHaveBeenCalled()
  })

  it('does nothing without a button', () => {
    document.body.innerHTML = ''

    expect(() => initFlushCache(document, fakeLocation(), reload)).not.toThrow()
  })

  it('flushes the page the browser is on by default', async () => {
    document.body.innerHTML = '<button type="button" data-flush-cache-button>Flush</button>'
    initFlushCache()
    fetchMock.mockRejectedValue(new TypeError('Failed to fetch'))

    button().click()
    await vi.waitFor(() => expect(button().disabled).toBe(false))

    expect(requestedUrl(fetchMock).origin).toBe(window.location.origin)
  })

  it('reloads the page the browser is on by default', async () => {
    const browserReload = vi.fn<() => void>()
    vi.stubGlobal('location', { href: PAGE_URL, reload: browserReload })
    document.body.innerHTML = '<button type="button" data-flush-cache-button>Flush</button>'
    initFlushCache()
    fetchMock.mockResolvedValue(textResponse(200, ''))

    button().click()

    await vi.waitFor(() => expect(browserReload).toHaveBeenCalledTimes(1))
    expect(requestedUrl(fetchMock).pathname).toBe('/about/')
  })
})
