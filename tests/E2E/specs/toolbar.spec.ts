import { expect, test } from '@playwright/test'
import { loadFixture } from '../helpers/fixtures'
import { anonymousContext, toolbar } from '../helpers/toolbar'

const PAGE_URL = '/e2e-published'

test.beforeEach(async ({ page }) => {
  await loadFixture(page.request, 'toolbar-page')
})

test('renders on a published page with the CMS version linking to the admin', async ({ page }) => {
  await page.goto(PAGE_URL)

  await expect(toolbar(page).locator('[data-toolbar-panel]')).toBeVisible()
  const cms = toolbar(page).getByRole('link', { name: /^\d+\.\d+/ })
  await expect(cms).toBeVisible()
  await expect(cms).toHaveAttribute('href', /(^|\/)admin\/?$/)
})

test('collapse state survives a reload', async ({ page }) => {
  await page.goto(PAGE_URL)
  const collapse = page.getByRole('button', { name: 'Show or hide the toolbar' })
  const panel = page.locator('[data-toolbar-panel]')
  await expect(panel).toBeVisible()

  await collapse.click()
  await expect(panel).toBeHidden()
  await expect(collapse).toHaveAttribute('aria-expanded', 'false')

  await page.reload()
  await expect(panel).toBeHidden()
  await expect(collapse).toHaveAttribute('aria-expanded', 'false')

  await collapse.click()
  await page.reload()
  await expect(panel).toBeVisible()
  await expect(collapse).toHaveAttribute('aria-expanded', 'true')
})

test('the toggles dialog opens from its button and closes on Escape', async ({ page }) => {
  await page.goto(PAGE_URL)
  const toggles = page.getByRole('dialog', { name: 'Toggles' })

  await toolbar(page).getByRole('button', { name: 'Toggles' }).click()
  await expect(toggles).toBeVisible()

  await page.keyboard.press('Escape')
  await expect(toggles).toBeHidden()
})

test('an anonymous visitor sees no toolbar', async ({ browser }) => {
  const context = await anonymousContext(browser)
  const anonymous = await context.newPage()

  await anonymous.goto(PAGE_URL)

  await expect(anonymous.getByRole('heading', { name: 'E2E Published' })).toBeVisible()
  await expect(anonymous.locator('[data-admin-toolbar]')).toHaveCount(0)
  await context.close()
})
