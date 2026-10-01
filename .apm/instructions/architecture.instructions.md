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
| `client/src/` → `client/dist/` | webpack + Tailwind 3 build (class prefix `ss-at-`); `client/dist` is committed |
| `lang/` | `en.yml`, `nl.yml` |
| `.docker/` | Test harness: SS6 app at `/app`, module mounted at `/module` |
| `.docker/app/` | Harness composer.json + committed lock, PHPStan/PHPUnit/Rector/Infection configs |
| `tests/Unit/`, `tests/Integration/`, `tests/Functional/` | PHPUnit suites `unit` (no DB), `integration` (DB fixtures), `functional` (HTTP) |
| `tests/Fixture/` | `TestOnly` components for discovery tests |

- Permission `ADMIN_TOOLBAR` gates rendering; members can opt out via `DisableAdminToolbar`.
- Components are discovered by type (`Component::discover()`): add one by declaring a concrete subclass, remove one with `enabled: false` YAML.
- Component data is config (`title`, `icon`, `order`, `enabled`, `javascript`, `stylesheets`, `hook`, `hidden_until_enabled`, `placement`, `menu`, `action`, `success_message`, `destructive`); behaviour is methods (`isSupported`, `appliesTo`, `isAllowedFor`, `perform`).
- `ToolbarContext` is the only source of page/member/request inside a component — never `Controller::curr()` or `Security::getCurrentUser()` in a component.
- Page actions: the Page menu and `PageActionController` share `PageActionItem` rules; the endpoint answers JSON `{message}` for every status (405, 400, 403, 404, 409, 200).
- Templates resolve by class ancestry (`templates/WeDevelop/AdminToolbar/<namespace path>.ss`, falling back to `Model/<Base>.ss`).
- Until the frontend phase lands, templates keep every class/id/`data-*` hook the old `client/dist` uses.
