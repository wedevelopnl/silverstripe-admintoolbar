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

## SilverStripe 6

- **`ModuleResourceLoader::resolveURL()` throws on missing files** — check `resolveResource($path)->exists()` first.
- **FunctionalTest login**: after `$memberId = $this->logInWithPermission(...)`, also `$this->session()->set('loggedInAs', $memberId)` or `$this->get()` runs anonymously.
