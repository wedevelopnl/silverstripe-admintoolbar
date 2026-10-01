import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'
import { defineConfig, devices } from '@playwright/test'

/**
 * Resolve the base URL from environment or .docker/.env.
 *
 * Priority: E2E_BASE_URL env var > WEB_PORT from .docker/.env
 */
function resolveBaseUrl(): string {
  if (process.env.E2E_BASE_URL) {
    return process.env.E2E_BASE_URL
  }

  const envPath = resolve(__dirname, '.docker/.env')
  try {
    const envContent = readFileSync(envPath, 'utf-8')
    const match = envContent.match(/^WEB_PORT=(\d+)$/m)
    if (match) {
      return `https://localhost:${match[1]}`
    }
  } catch {
    // .docker/.env not generated yet — fall through
  }

  throw new Error('Cannot determine base URL. Set E2E_BASE_URL or run .docker/env.sh first.')
}

export default defineConfig({
  testDir: './tests/E2E/specs',
  fullyParallel: false,
  workers: 1,
  // Two retries in CI, one locally. The app container is set to restart on
  // failure (.docker/compose.yml), and a restart costs roughly one test timeout
  // to come back — so the first retry after a crash still lands on a dead port
  // and only the second can pass. Locally a crash is visible and worth stopping
  // on, so the extra attempt would only slow the feedback loop.
  retries: process.env.CI ? 2 : 1,
  reporter: 'html',

  use: {
    baseURL: resolveBaseUrl(),
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    // Dev environment uses self-signed certificates
    ignoreHTTPSErrors: true,
  },

  projects: [
    {
      name: 'setup-chromium',
      testDir: './tests/E2E',
      testMatch: /global\.setup\.ts/,
      use: devices['Desktop Chrome'],
    },
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome'],
        storageState: 'tests/E2E/.auth/admin.json',
      },
      dependencies: ['setup-chromium'],
    },
    // Firefox only runs in CI (via --project flag)
    ...(process.env.CI
      ? [
          {
            name: 'setup-firefox',
            testDir: './tests/E2E',
            testMatch: /global\.setup\.ts/,
            use: devices['Desktop Firefox'],
          },
          {
            name: 'firefox',
            use: {
              ...devices['Desktop Firefox'],
              storageState: 'tests/E2E/.auth/admin.json',
            },
            dependencies: ['setup-firefox'],
          },
        ]
      : []),
  ],
})
