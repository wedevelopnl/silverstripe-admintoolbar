import { expect, type Page, test } from '@playwright/test'
import { createFixtureClient } from '@wedevelop/e2e'
import { toolbar } from '../helpers/toolbar'

const fixtures = createFixtureClient()

test.beforeEach(async ({ page }) => {
  await fixtures.load(page.request, 'toolbar-page')
  await page.goto('/e2e-published')
})

/** Switches a toggle on; the toolbar reloads the page to apply it. */
async function enable(page: Page, name: string): Promise<void> {
  await toolbar(page).getByRole('button', { name: 'Toggles' }).click()
  const reload = page.waitForEvent('load')
  await page.getByRole('dialog', { name: 'Toggles' }).getByText(name, { exact: true }).click()
  await reload
}

test('the timing toggle reveals the page load time', async ({ page }) => {
  const button = page.locator('[data-timing-button]')
  await expect(button).toBeHidden()

  await enable(page, 'Timing')

  await expect(button).toBeVisible()
  await expect(button).toHaveText(/^\s*\d+ ms\s*$/)
})

test('the queries toggle reveals the queries the page ran', async ({ page }) => {
  const button = page.locator('[data-queries-button]')
  await expect(button).toBeHidden()

  await enable(page, 'Queries')

  await expect(button).toBeVisible()
  await expect(button).toHaveText(/^\s*\d+ ms \([1-9]\d* queries\)\s*$/)

  await button.click()
  const dialog = page.getByRole('dialog', { name: 'Queries' })
  await expect(dialog).toBeVisible()
  await expect(
    dialog
      .getByRole('listitem')
      .filter({ hasText: /SELECT/ })
      .first(),
  ).toBeVisible()
})
