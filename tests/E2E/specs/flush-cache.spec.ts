import { expect, test } from '@playwright/test'
import { loadFixture } from '../helpers/fixtures'
import { toolbar } from '../helpers/toolbar'

test.beforeEach(async ({ page }) => {
  await loadFixture(page.request, 'toolbar-page')
})

test('flushing the cache requests a flush and reloads the page', async ({ page }) => {
  await page.goto('/e2e-published')

  const flush = page.waitForRequest(
    (request) => new URL(request.url()).searchParams.get('flush') === '1',
  )
  const reload = page.waitForResponse(
    (response) =>
      response.request().isNavigationRequest() && response.url().endsWith('/e2e-published'),
  )
  await toolbar(page).getByRole('button', { name: 'Flush cache' }).click()

  await flush
  expect((await reload).status()).toBe(200)
  await expect(toolbar(page).locator('[data-toolbar-panel]')).toBeVisible()
})
