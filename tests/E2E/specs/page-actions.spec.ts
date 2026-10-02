import { expect, type Page, test } from '@playwright/test'
import { createFixtureClient, type FixtureLoadResponse } from '@wedevelop/e2e'
import { anonymousContext, openMenu } from '../helpers/toolbar'

const fixtures = createFixtureClient()

const PUBLISHED_URL = '/e2e-published'
const DRAFT_URL = '/e2e-draft'

let fixture: FixtureLoadResponse

test.beforeEach(async ({ page }) => {
  fixture = await fixtures.load(page.request, 'toolbar-page')
})

function pageId(identifier: string): number {
  const id = fixture.fixtureMap.Page?.[identifier]
  expect(id).toBeDefined()

  return id ?? 0
}

/** The next full load of `path`: the toolbar reloads the page after an action. */
function nextLoadOf(page: Page, path: string) {
  return page.waitForResponse(
    (response) =>
      response.request().isNavigationRequest() && new URL(response.url()).pathname === path,
  )
}

test('unpublishing a published page leaves it as a draft', async ({ page, browser }) => {
  await page.goto(`${PUBLISHED_URL}?stage=Stage`)
  const menu = await openMenu(page, 'PageMenu')
  await expect(menu.getByText('Published', { exact: true })).toBeVisible()
  await expect(menu.getByRole('button', { name: 'Unpublish', exact: true })).toBeVisible()
  await expect(menu.getByRole('button', { name: 'Unpublish and archive' })).toBeVisible()

  const reload = nextLoadOf(page, PUBLISHED_URL)
  await menu.getByRole('button', { name: 'Unpublish', exact: true }).click()
  expect((await reload).status()).toBe(200)

  const reopened = await openMenu(page, 'PageMenu')
  await expect(reopened.getByText('Draft', { exact: true })).toBeVisible()
  await expect(reopened.getByRole('button', { name: 'Archive', exact: true })).toBeVisible()

  const context = await anonymousContext(browser)
  expect((await context.request.get(PUBLISHED_URL)).status()).toBe(404)
  await context.close()
})

test('archiving a draft removes it from the stage', async ({ page }) => {
  await page.goto(`${DRAFT_URL}?stage=Stage`)
  const menu = await openMenu(page, 'PageMenu')

  const reload = nextLoadOf(page, DRAFT_URL)
  await menu.getByRole('button', { name: 'Archive', exact: true }).click()

  expect((await reload).status()).toBe(404)
})

test('a page changed elsewhere answers with a conflict message', async ({ page }) => {
  await page.goto(`${PUBLISHED_URL}?stage=Stage`)
  const menu = await openMenu(page, 'PageMenu')
  const actions = menu.locator('[data-page-actions]')
  const token = await actions.locator('input[name="SecurityID"]').inputValue()
  const endpoint = await actions.getAttribute('data-endpoint')

  // Another editor unpublishes the page while this menu is open.
  const elsewhere = await page.request.post(endpoint ?? '', {
    data: { page_id: pageId('e2e_published'), action: 'unpublish' },
    headers: { 'X-SecurityID': token },
  })
  expect(elsewhere.status()).toBe(200)

  const unpublish = menu.getByRole('button', { name: 'Unpublish', exact: true })
  const response = page.waitForResponse(
    (r) => r.request().method() === 'POST' && r.url().endsWith('/admintoolbaraction/pageAction'),
  )
  await unpublish.click()
  const conflict = await response
  const { message } = (await conflict.json()) as { message: string }

  expect(conflict.status()).toBe(409)
  await expect(actions.locator('[data-action-message]')).toHaveText(message)
  await expect(menu).toBeVisible()
  await expect(unpublish).toBeEnabled()
})
