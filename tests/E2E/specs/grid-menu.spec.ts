import { expect, test } from '@playwright/test'
import { type FixtureLoadResponse, loadFixture } from '../helpers/fixtures'
import { openMenu } from '../helpers/toolbar'

const CONTENT_ELEMENT = 'WeDevelop\\Grid\\Model\\ContentElement'
const SHARED_BLOCK = 'WeDevelop\\Grid\\Model\\SharedBlock'

test.describe('grid page', () => {
  let fixture: FixtureLoadResponse

  test.beforeEach(async ({ page }) => {
    fixture = await loadFixture(page.request, 'grid-page')
    await page.goto('/e2e-grid')
  })

  test('maps the grid with proportional columns and the shared block as a leaf', async ({
    page,
  }) => {
    const menu = await openMenu(page, 'GridMenu')

    await expect(menu.locator('[data-grid-node="section"]')).toContainText('Hero')
    const columns = menu.locator('[data-grid-node="column"]')
    await expect(columns).toHaveCount(2)
    const wide = await columns.nth(0).boundingBox()
    const narrow = await columns.nth(1).boundingBox()
    expect(wide).not.toBeNull()
    expect(narrow).not.toBeNull()
    // 8/12 against 4/12; the gaps between tracks skew it slightly.
    expect((wide?.width ?? 0) / (narrow?.width ?? 1)).toBeGreaterThan(1.8)
    expect((wide?.width ?? 0) / (narrow?.width ?? 1)).toBeLessThan(2.2)

    const shared = menu.locator('[data-grid-node="shared"]')
    await expect(shared).toHaveCount(1)
    await expect(shared).toContainText('Banner')
    await expect(shared.locator('[data-grid-node]')).toHaveCount(0)
  })

  test("an element's link opens its editor in the page's grid", async ({ page, context }) => {
    const menu = await openMenu(page, 'GridMenu')
    const introId = fixture.fixtureMap[CONTENT_ELEMENT]?.intro

    const tab = context.waitForEvent('page')
    await menu.getByRole('link', { name: 'Intro' }).click()
    const editor = await tab
    await editor.waitForLoadState()

    expect(editor.url()).toContain('/admin/pages/edit/EditForm/')
    expect(editor.url()).toContain(`/field/GridEditor/item/${introId}/`)
  })

  test("the shared block's link opens it in the shared-block library", async ({
    page,
    context,
  }) => {
    const menu = await openMenu(page, 'GridMenu')
    const blockId = fixture.fixtureMap[SHARED_BLOCK]?.e2e_banner
    const link = menu.getByRole('link', { name: 'Banner' })
    const href = (await link.getAttribute('href')) ?? ''

    expect(href).toContain(`/WeDevelop-Grid-Model-SharedBlock/item/${blockId}/`)
    expect((await page.request.get(href)).status()).toBe(200)

    const tab = context.waitForEvent('page')
    await link.click()
    const library = await tab
    await library.waitForLoadState()
    expect(library.url()).toContain(href)
  })
})

test('a multi-zone page lists every zone under its own heading', async ({ page }) => {
  await loadFixture(page.request, 'multi-zone')
  await page.goto('/e2e-multi-zone')

  const menu = await openMenu(page, 'GridMenu')

  await expect(menu.locator('[data-grid-zone]')).toHaveCount(2)
  await expect(menu.locator('[data-grid-zone="main"]').getByRole('heading')).toHaveText(
    'Zone: main',
  )
  await expect(menu.locator('[data-grid-zone="sidebar"]').getByRole('heading')).toHaveText(
    'Zone: sidebar',
  )
})
