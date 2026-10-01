export const PAGE_URL = 'https://example.com/about/?stage=Live'

/** Only `href` is read by the modules; the rest of Location stays unimplemented. */
export function fakeLocation(href: string = PAGE_URL): Location {
  return { href } as Location
}

export function byHook<T extends HTMLElement = HTMLElement>(hook: string): T {
  const element = document.querySelector<T>(`[${hook}]`)
  if (!element) {
    throw new Error(`No element with ${hook} in the fixture`)
  }
  return element
}

export function jsonResponse(status: number, body: unknown): Response {
  return new Response(JSON.stringify(body), {
    status,
    headers: { 'Content-Type': 'application/json' },
  })
}

export function textResponse(status: number, body: string): Response {
  return new Response(body, { status, headers: { 'Content-Type': 'text/html' } })
}

/** The URL a stubbed `fetch` was called with, as a parsed URL. */
export function requestedUrl(fetchMock: { mock: { calls: unknown[][] } }, call = 0): URL {
  return new URL(String(fetchMock.mock.calls[call]?.[0]))
}
