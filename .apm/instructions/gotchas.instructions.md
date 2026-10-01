---
description: Important gotchas and caveats to avoid common mistakes
applyTo: "**/*"
---

# Gotchas

## Tooling

- **`CLAUDE.md`, `AGENTS.md` and `GEMINI.md` are generated** from `.apm/instructions/` by `apm compile -t claude,agents,gemini --local-only`. Edit the sources, never the generated files.
- **The harness installs from a COMMITTED lock — never hand-edit it.** `.docker/app/composer.lock` is COPYed into the image. Regenerate with `task relock` (resolves inside the image); a weekly workflow raises the same change as a PR.
- **Composer does not re-check a path package's requires when installing from a lock.** Adding a `require` to the module's `composer.json` without `task relock` installs nothing and exits 0; `task verify-lock` (a CI gate) catches it.
- **`config.platform.php` in `.docker/app/composer.json` is pinned to `8.3.0`** — one lock serves the 8.3/8.4/8.5 matrix, so it must hold a set installable on the lowest.
- **vendor-plugin cannot expose a path-repo module outside `/app`** (realpath breaks the relative path). `.docker/entrypoint.sh` symlinks `_resources/vendor/wedevelopnl/silverstripe-admintoolbar/client/dist` by hand.
- **PHPUnit config schema path must be relative** (`../app/vendor/...`) — Infection prepends the config dir to it.
- **Infection ignores only i18n-key concatenation** (`Concat`/`ConcatOperandRemoval` on `_t(self::class . '.KEY', …)` lines, `.docker/app/infection.json5`). Any other escape: delete the dead code, else kill it with a test, else suppress the narrowest scope with an inline equivalence proof.
- **Vitest is pinned to 4** — `@stryker-mutator/vitest-runner` 10.0.0 runs 0 tests per mutant under Vitest 5 (every mutant survives). Dependabot ignores the major; lift both once a runner release supports Vitest 5.

## Frontend

- **Tailwind 4 prefixes are letters only** — `ssat`, never `ss-at`.
- **Never run a build while a browser test, the dev server or the PHP functional suite reads `client/dist`** — Vite empties it first.
- **Same-property utilities sort alphabetically** — `ssat:hidden` beats `ssat:flex` and `ssat:btn` but loses to `ssat:inline-flex`/`ssat:inline-block`; don't pair those with a script-toggled `ssat:hidden`.
- **Deleting and recreating `client/dist` orphans the container's bind mount** — `docker compose -f .docker/compose.yml restart app`.

## SilverStripe 6

- **`ModuleResourceLoader::resolveURL()` throws on missing files** — check `resolveResource($path)->exists()` first.
- **FunctionalTest login**: after `$memberId = $this->logInWithPermission(...)`, also `$this->session()->set('loggedInAs', $memberId)` or `$this->get()` runs anonymously.
- **`TestOnly` fixture components ship with `enabled: false`** — the test kernel puts them in the manifest, so an enabled fixture would render in every functional test. Enable them per test with `Config::modify()`.
- **Versioned 3 has no `canArchive()`** — archive permission is `canDelete($member)`.
- **`FunctionalTest` disables CSRF tokens in `setUp()`** — call `SecurityToken::enable()` in a test that exercises token checks.
- **`SapphireTest` loads fixtures before it logs in its default admin** — a fixture record's version author is whichever member the previous test left logged in. Write the version inside the test when its author matters.
- **The Injector caches each service's config spec across tests** — after `Config::modify()` on `Injector`, give the test injector a fresh `SilverStripeServiceConfigurationLocator`.
- **silverstan already stubs `SilverStripe\Versioned\Versioned`** — a second stub for the same class is a non-ignorable PHPStan error.
