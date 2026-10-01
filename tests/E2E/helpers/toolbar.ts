import {
  type Browser,
  type BrowserContext,
  expect,
  type Locator,
  type Page,
} from '@playwright/test'

/** The toolbar root. It has no box of its own (its children are fixed); assert visibility on `[data-toolbar-panel]`. */
export function toolbar(page: Page): Locator {
  return page.locator('[data-admin-toolbar]')
}

/** Opens a menu's dialog from its toolbar button and returns the open dialog. */
export async function openMenu(page: Page, dialogId: string): Promise<Locator> {
  await toolbar(page).locator(`button[data-toggle-dialog="${dialogId}"]`).first().click()
  const dialog = page.locator(`dialog#${dialogId}`)
  await expect(dialog).toBeVisible()

  return dialog
}

/**
 * A browser context without the admin session. Contexts created in a test
 * inherit the project's `use` options, storage state included, so it is reset.
 */
export function anonymousContext(browser: Browser): Promise<BrowserContext> {
  return browser.newContext({ storageState: { cookies: [], origins: [] } })
}
