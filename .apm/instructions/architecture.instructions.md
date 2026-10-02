---
description: Directory layout and where each concern lives
applyTo: "**/*"
---

# Architecture

| Path | Contents |
|---|---|
| `src/AdminToolbar.php` | Renders the toolbar from discovered menus, buttons and toggles; `PermissionProvider` (`ADMIN_TOOLBAR`) |
| `src/ToolbarContext.php` | `final readonly`: `?SiteTree $page`, `Member $member`, `HTTPRequest $request` |
| `src/Model/` | Abstract `Component` and its bases `Menu`, `MenuItem`, `Button`, `Toggle` |
| `src/Button/`, `src/Toggle/` | Flush cache, Queries, Timing |
| `src/Menu/` | `PageMenu`, `CMSMenu`, `UserMenu` |
| `src/Menu/Page/` | `PublishState` enum; `PageActionItem` and its Unpublish, Archive, UnpublishAndArchive items |
| `src/Menu/User/` | `UsernameItem`, `EditUserItem` |
| `src/Controller/PageActionController.php` | `admintoolbaraction/pageAction` — page-action endpoint |
| `src/Extension/` | `ContentControllerExtension` (`$AdminToolbar` template hook), `MemberExtension` (per-member settings) |
| `templates/WeDevelop/AdminToolbar/` | `.ss` templates mirroring the `src/` namespaces |
| `client/src/ts/` → `client/dist/js/toolbar.js` | TypeScript behaviour modules, one IIFE bundle via Vite; tests beside each module |
| `client/src/styles/` → `client/dist/css/toolbar.css` | Tailwind 4 (prefix `ssat`) via the Tailwind CLI; `icons.css` is a verbatim `silverstripe/admin` snapshot |
| `client/fonts/` → `client/dist/fonts/` | Self-hosted Roboto woff2 + `silverstripe.woff`, copied verbatim |
| `client/dist/` | Committed build output; CI and `.claude/hooks/qa-gate.sh` fail on drift |
| `lang/` | `en.yml`, `nl.yml` |
| `.docker/` | Test harness: SS6 app at `/app`, module mounted at `/module` |
| `.docker/app/` | Harness composer.json + committed lock, PHPStan/PHPUnit/Rector/Infection configs |
| `tests/Unit/`, `tests/Integration/`, `tests/Functional/` | PHPUnit suites `unit` (no DB), `integration` (DB fixtures), `functional` (HTTP) |
| `tests/Fixture/` | `TestOnly` components for discovery tests |
| `tests/E2E/{specs,helpers,Fixture}` | Playwright specs, helpers, YAML fixtures; fixtures registered in `_config/dev.yml` (`Only: environment: dev`, export-ignored) |
| `playwright.config.ts` | Base URL from `E2E_BASE_URL` or `.docker/.env`; setup project stores the admin session in `tests/E2E/.auth/` |

- Permission `ADMIN_TOOLBAR` gates rendering; members can opt out via `DisableAdminToolbar`.
- Components are discovered by type (`Component::discover()`): add one by declaring a concrete subclass, remove one with `enabled: false` YAML.
- Component data is config (`title`, `icon`, `order`, `enabled`, `javascript`, `stylesheets`, `hook`, `hidden_until_enabled`, `placement`, `menu`, `action`, `success_message`, `destructive`); behaviour is methods (`isSupported`, `appliesTo`, `isAllowedFor`, `perform`).
- `ToolbarContext` is the only source of page/member/request inside a component — never `Controller::curr()` or `Security::getCurrentUser()` in a component.
- Page actions: the Page menu and `PageActionController` share `PageActionItem` rules; the endpoint answers JSON `{message}` for every status (405, 400, 403, 404, 409, 200).
- Templates resolve by class ancestry (`templates/WeDevelop/AdminToolbar/<namespace path>.ss`, falling back to `Model/<Base>.ss`).
- `AdminToolbar` requires `client/dist/css/toolbar.css` and `client/dist/js/toolbar.js`; built-in components declare no `javascript`.

## DOM contract (templates ↔ `client/src/ts`)

| Hook | Where |
|---|---|
| `#admin-toolbar` + `data-admin-toolbar` | Root in `AdminToolbar.ss`; dialogs and their triggers only work inside it |
| `data-toolbar-panel` (`#admin-toolbar-panel`) | The bar, rendered `ssat:hidden` |
| `data-toggle-admin-toolbar` | Collapse button (`aria-expanded`, `aria-controls`) |
| `data-toggle-dialog="<id>"` | Toggles `dialog#<id>` |
| `data-dialog-anchor` | Wrapper a dialog is positioned at before opening |
| `data-page-actions` + `data-endpoint` + `data-error-message` | `PageMenu.ss` wrapper holding `input[name="SecurityID"]` and the actions |
| `button[data-action][data-page-id]` | One page action (`PageActionItem.ss`) |
| `data-action-message` | Error text inside `data-page-actions`, rendered `ssat:hidden` |
| `data-flush-cache-button` | `FlushCacheButton` |
| `data-queries-toggle` / `data-timing-toggle` | Toggle checkboxes |
| `data-queries-button` / `data-timing-button` + `data-summary` | Buttons rendered `ssat:hidden`; `data-summary` holds the `{ms}`/`{count}` label template |
| `data-button-label` | Label `<span>` in `Model/Button.ss` |

- The flush/queries/timing hooks live in each component's `hook` config (the full attribute name), printed as `$Hook` by `Model/Button.ss` / `Model/Toggle.ss` — grep `src/`, not `templates/`.
