import { test as setup } from '@playwright/test'
import { authenticateAdmin } from '@wedevelop/e2e'

const AUTH_FILE = 'tests/E2E/.auth/admin.json'

setup('authenticate as admin', async ({ page }) => {
  await authenticateAdmin(page, { storageStatePath: AUTH_FILE })
})
