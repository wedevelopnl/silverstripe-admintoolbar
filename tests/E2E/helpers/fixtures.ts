import type { APIRequestContext } from '@playwright/test'
import { createFixtureClient, type FixtureLoadResponse } from '@wedevelop/e2e'

export type { FixtureLoadResponse, FixtureMap } from '@wedevelop/e2e'

/**
 * Fixture client from the wedevelopnl/silverstripe-e2e module. Fixtures are
 * registered in _config/dev.yml; loading one purges every Page, GridElement
 * and SharedBlock in the dev database first.
 */
const client = createFixtureClient()

export function loadFixture(
  request: APIRequestContext,
  name: string,
): Promise<FixtureLoadResponse> {
  return client.load(request, name)
}

export function resetFixtures(request: APIRequestContext): Promise<void> {
  return client.reset(request)
}
