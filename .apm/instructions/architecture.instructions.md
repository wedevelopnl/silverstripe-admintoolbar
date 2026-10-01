---
description: Directory layout and where each concern lives
applyTo: "**/*"
---

# Architecture

| Path | Contents |
|---|---|
| `src/AdminToolbar.php` | Renders the toolbar; discovers menus, buttons and toggles via `ClassInfo::implementorsOf(<…>ProviderInterface)` |
| `src/Menus/` | Page, CMS and User menus and their items |
| `src/Buttons/`, `src/Toggles/` | Flush cache, Queries, Timing |
| `src/Controllers/AdminToolbarActionController.php` | `admintoolbaraction/pageAction` — unpublish/archive endpoint |
| `src/Extensions/` | `SiteTreeExtension` (`$AdminToolbar` template hook), `MemberExtension` (per-member settings) |
| `templates/WeDevelop/AdminToolbar/` | `.ss` templates mirroring the `src/` namespaces |
| `client/src/` → `client/dist/` | webpack + Tailwind 3 build (class prefix `ss-at-`); `client/dist` is committed |
| `lang/` | `en.yml`, `nl.yml` |
| `.docker/` | Test harness: SS6 app at `/app`, module mounted at `/module` |
| `.docker/app/` | Harness composer.json + committed lock, PHPStan/PHPUnit/Rector configs |
| `phpstan/baseline.neon` | Temporary baseline for pre-redesign code |
| `tests/Functional/` | HTTP tests (`FunctionalTest`) |

- Permission `ADMIN_TOOLBAR` gates rendering; members can opt out via `DisableAdminToolbar`.
