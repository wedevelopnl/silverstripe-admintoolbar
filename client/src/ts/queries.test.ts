import { byHook, fakeLocation, requestedUrl, textResponse } from './testing/dom'
import { initQueries, parseQueries } from './queries'

const TOOLBAR = `
  <div id="admin-toolbar" data-admin-toolbar>
    <button type="button" data-queries-button data-summary="{ms} ms ({count} queries)" class="ssat:btn ssat:hidden">
      <span data-button-label>Queries</span>
    </button>
  </div>`

// As `Database::displayQuery()` prints them through `Debug::message()`.
const QUERY_PAGE = `<html><body>
  <p class="alert alert-warning">

0001: SELECT * FROM "SiteTree" WHERE "Sort" > 1.5
0.0012s
</p>
  <p class="alert alert-warning">

0002: SELECT '&lt;script&gt;alert(1)&lt;/script&gt;'
0.0030s
</p>
  <p class="alert alert-info">Not a query 9.9s</p>
  <main>Page</main>
</body></html>`

const button = () => byHook<HTMLButtonElement>('data-queries-button')
const label = () => byHook('data-button-label')

describe('parseQueries', () => {
  it('reads each query and its timing', () => {
    expect(parseQueries(QUERY_PAGE)).toEqual([
      { sql: '0001: SELECT * FROM "SiteTree" WHERE "Sort" > 1.5\n0.0012s', seconds: 0.0012 },
      { sql: "0002: SELECT '<script>alert(1)</script>'\n0.0030s", seconds: 0.003 },
    ])
  })

  it('reads 0 seconds when a query has no timing', () => {
    expect(parseQueries('<p class="alert alert-warning">SELECT 1</p>')).toEqual([
      { sql: 'SELECT 1', seconds: 0 },
    ])
  })

  it('reads whole-second and multi-second timings at the end of the query only', () => {
    expect(
      parseQueries(`
        <p class="alert alert-warning">0003: SELECT '3s'
12.5s</p>
        <p class="alert alert-warning">0004: SELECT 1
2s</p>`),
    ).toEqual([
      { sql: "0003: SELECT '3s'\n12.5s", seconds: 12.5 },
      { sql: '0004: SELECT 1\n2s', seconds: 2 },
    ])
  })

  it('reads no queries from a page without any', () => {
    expect(parseQueries('<main>Page</main>')).toEqual([])
  })
})

describe('initQueries', () => {
  let fetchMock: ReturnType<typeof vi.fn<typeof fetch>>

  beforeEach(() => {
    document.body.innerHTML = TOOLBAR
    fetchMock = vi.fn<typeof fetch>()
    vi.stubGlobal('fetch', fetchMock)
  })

  it('does nothing while the setting is off', async () => {
    await initQueries(document, fakeLocation())

    expect(fetchMock).not.toHaveBeenCalled()
    expect(button().classList.contains('ssat:hidden')).toBe(true)
  })

  it('does nothing without a button', async () => {
    localStorage.setItem('[data-queries-toggle]', 'true')
    document.body.innerHTML = ''

    await initQueries(document, fakeLocation())

    expect(fetchMock).not.toHaveBeenCalled()
  })

  describe('when enabled', () => {
    beforeEach(() => {
      localStorage.setItem('[data-queries-toggle]', 'true')
    })

    it('reveals the button and summarises the queries of the current page', async () => {
      fetchMock.mockResolvedValue(textResponse(200, QUERY_PAGE))

      await initQueries(document, fakeLocation('https://example.com/about/?stage=Live'))

      expect(button().classList.contains('ssat:hidden')).toBe(false)
      const url = requestedUrl(fetchMock)
      expect(url.pathname).toBe('/about/')
      expect(url.searchParams.get('stage')).toBe('Live')
      expect(url.searchParams.get('showqueries')).toBe('inline')
      expect(url.searchParams.get('AdminToolbarDisabled')).toBe('1')
      expect(fetchMock.mock.calls[0]?.[1]).toMatchObject({ credentials: 'same-origin' })
      expect(label().textContent).toBe('4 ms (2 queries)')
    })

    it('empties the label when an overridden template renders no summary', async () => {
      delete button().dataset.summary
      fetchMock.mockResolvedValue(textResponse(200, QUERY_PAGE))

      await initQueries(document, fakeLocation())

      expect(label().textContent).toBe('')
    })

    it('opens a dialog inside the toolbar listing each query as text', async () => {
      fetchMock.mockResolvedValue(textResponse(200, QUERY_PAGE))
      await initQueries(document, fakeLocation())

      button().click()

      const dialog = document.querySelector('#admin-toolbar dialog') as HTMLDialogElement
      expect(dialog.open).toBe(true)
      expect(dialog.getAttribute('aria-label')).toBe('Queries')
      const items = [...dialog.querySelectorAll('li > code')].map((code) => code.textContent)
      expect(items).toEqual([
        '0001: SELECT * FROM "SiteTree" WHERE "Sort" > 1.5\n0.0012s',
        "0002: SELECT '<script>alert(1)</script>'\n0.0030s",
      ])
      expect(dialog.querySelector('script')).toBeNull()
    })

    it('keeps the original label when the request fails', async () => {
      fetchMock.mockRejectedValue(new TypeError('Failed to fetch'))

      await expect(initQueries(document, fakeLocation())).resolves.toBeUndefined()

      expect(button().classList.contains('ssat:hidden')).toBe(false)
      expect(label().textContent).toBe('Queries')
      expect(document.querySelector('#admin-toolbar dialog')).toBeNull()
    })

    it('queries the page the browser is on by default', async () => {
      fetchMock.mockResolvedValue(textResponse(200, QUERY_PAGE))

      await initQueries()

      expect(requestedUrl(fetchMock).origin).toBe(window.location.origin)
      expect(label().textContent).toBe('4 ms (2 queries)')
    })

    it('still offers the dialog when an overridden template has no label element', async () => {
      byHook('data-button-label').remove()
      fetchMock.mockResolvedValue(textResponse(200, QUERY_PAGE))
      await initQueries(document, fakeLocation())

      button().click()

      expect(document.querySelectorAll('#admin-toolbar dialog li')).toHaveLength(2)
      expect(document.querySelector('#admin-toolbar dialog')?.getAttribute('aria-label')).toBe('')
    })

    it('keeps the original label when the page answers an error', async () => {
      fetchMock.mockResolvedValue(textResponse(500, QUERY_PAGE))

      await initQueries(document, fakeLocation())

      expect(label().textContent).toBe('Queries')
    })
  })
})
