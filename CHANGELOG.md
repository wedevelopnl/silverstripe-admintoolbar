# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed

- Opening a menu no longer draws a focus ring around its close button in Safari: the dialog itself takes focus, and the first Tab reaches the close button.
- Toolbar buttons and toggles show a focus ring when reached by keyboard. Every toolbar control now shares one ring in the toolbar's primary colour.

## [6.0.0-rc.1] - 2026-10-02

The first release for Silverstripe 6, and a ground-up rewrite. The toolbar is now built from **components discovered by type**: a module or project adds a menu, menu item, button or toggle by declaring a class, and removes one with `enabled: false` in YAML. The provider interfaces and the name-based `disabled_*` lists of 2.x are gone. The version jumps from 2.0.6 to 6.0.0 so the package major matches the Silverstripe major, the scheme `wedevelopnl/silverstripe-grid` uses.

Releases up to 2.0.6 (Silverstripe 4 and 5) predate this changelog. The 2.x line stays on `main`.

### Upgrading

From 2.x, in order:

1. **Require the release candidate explicitly.** `composer require wedevelopnl/silverstripe-admintoolbar:^6.0@RC` (or lower the project's `minimum-stability`). It needs PHP ^8.3 and Silverstripe CMS ^6.0.
2. **Run `sake db:build --flush`.** Every class moved to a new namespace (`Extensions\` → `Extension\`, `Controllers\` → `Controller\`, `Menus\` → `Menu\`, `Buttons\` → `Button\`, `Toggles\` → `Toggle\`), so a stale manifest still names classes that no longer exist. The `Member` columns `DisableAdminToolbar` and `AdminToolbarDefaultCollapsed` are unchanged and keep their values.
3. **Replace `disabled_menus`, `disabled_menu_items`, `disabled_buttons` and `disabled_toggles`.** These lists on `WeDevelop\AdminToolbar\AdminToolbar` are no longer read, so a project that switched something off with them now shows it again, without an error. Set `enabled: false` on the component's class instead:

   | 2.x name | 6.x class |
   |---|---|
   | `Page` (menu) | `WeDevelop\AdminToolbar\Menu\PageMenu` |
   | `UnpublishMenuItem` | `WeDevelop\AdminToolbar\Menu\Page\UnpublishPageItem` |
   | `ArchivePage` | `WeDevelop\AdminToolbar\Menu\Page\ArchivePageItem` |
   | `UnpublishAndArchivePage` | `WeDevelop\AdminToolbar\Menu\Page\UnpublishAndArchivePageItem` |
   | `Menu` (CMS menu) | `WeDevelop\AdminToolbar\Menu\CMSMenu` |
   | `User` (menu) | `WeDevelop\AdminToolbar\Menu\UserMenu` |
   | `Username` / `EditUser` | `WeDevelop\AdminToolbar\Menu\User\UsernameItem` / `EditUserItem` |
   | `Flush Cache` | `WeDevelop\AdminToolbar\Button\FlushCacheButton` |
   | `Queries` (button / toggle) | `WeDevelop\AdminToolbar\Button\QueriesButton` / `Toggle\QueriesToggle` |
   | `Timing` (button / toggle) | `WeDevelop\AdminToolbar\Button\TimingButton` / `Toggle\TimingToggle` |

   `EditPage` and `AddToCampaign` have no counterpart. See Changed and Removed.
4. **Port custom components to the component bases.** A class implementing `AdminToolbarMenuProviderInterface`, `AdminToolbarMenuItemProviderInterface`, `AdminToolbarButtonProviderInterface` or `AdminToolbarToggleProviderInterface` is no longer discovered. Extend `WeDevelop\AdminToolbar\Model\Menu`, `MenuItem`, `Button` or `Toggle` instead. The README's "Adding a component from your module or project" section has worked examples.
5. **Check overridden templates.** Templates moved with their classes (`Menus/Page/PageMenu.ss` → `Menu/PageMenu.ss`, `Models/AdminToolbarButton.ss` → `Model/Button.ss`), and the stylesheet's Tailwind prefix changed from `ss-at-` to `ssat:`. A theme override at an old path is silently ignored, and one copied from 2.x renders unstyled.

`$AdminToolbar` stays the template variable. It is now provided by `ContentController` rather than `SiteTree`, so it resolves in a page template's top scope. Inside a `<% with %>` or `<% loop %>` block, use `$Top.AdminToolbar`.

Members keep their collapsed/expanded state and their Queries/Timing toggles: the toolbar reads the same `localStorage` keys 2.x wrote.

### Added

- **A component model for extending the toolbar.** Every concrete subclass of `Menu`, `MenuItem`, `Button` or `Toggle` is discovered and rendered, sorted by `order`. Components are configured through config (`title`, `icon`, `order`, `enabled`, `placement`, `menu`, `javascript`, `stylesheets`) and behave through methods (`isSupported()`). They read the current page, member and request from a `ToolbarContext` instead of global state. A menu item names its menu by class, and a subclass of that menu keeps the item. A component renders with the template matching its class and falls back to its base's template. An Injector replacement for a built-in component is rendered once.
- **Menus can be placed at the end of the bar** with `placement: end`, to the right of the toggles. The User menu uses it.
- **The Page menu shows the page's publish state** as a Published / Modified / Draft / Archived badge.
- **Page actions report their outcome.** A failed unpublish or archive shows the server's message inside the Page menu, falling back to a translated generic error when there is no response or it is not JSON. A successful one reloads the page.
- **Dutch translations** for every string the toolbar renders, including the page-action messages and the diagnostics labels. The Queries and Timing summaries (`{ms} ms ({count} queries)`) were hard-coded English in 2.x.

### Changed

- **Page actions follow the member's own permissions on the page.** Unpublish needs `canUnpublish`, Archive and Unpublish-and-archive need `canDelete` (Versioned 3 has no `canArchive`), and an action is only offered when the page's state admits it. You can't unpublish a draft-only page, for example. The menu and the endpoint read the same rules from `PageActionItem`, so the menu never offers an action the endpoint would refuse.
- **The page-action endpoint answers JSON `{message}` for every outcome**: 405 for a non-POST, 400 for a bad token or malformed body, 403 when not allowed, 404 for a missing page, 409 when the page changed since it was rendered, and 200 on success. Denials are logged as warnings with the reason, member, action and page ID. Messages are translated.
- **"Edit page" sits in the Page menu header** rather than in the menu's item list, and appears only when the member can edit the page. 2.x always showed it.
- **The "Last edited by" author links to their Security admin record only when the member may view the Security admin.**
- **The toolbar's CSS is built with Tailwind 4 under the `ssat` prefix** and ships as one stylesheet, `client/dist/css/toolbar.css`. Roboto and the Silverstripe icon font are self-hosted. Only the utilities the toolbar's own templates use are included, so a custom component template that needs other styles must ship them through its `stylesheets` config.
- **The toolbar's behaviour ships as one script**, `client/dist/js/toolbar.js`, instead of one file per button. The built-in components declare no `javascript` of their own.
- **Requirements**: PHP ^8.3 (was ^8.1), and Silverstripe framework ^6.0, CMS ^6.0, admin ^3.0, versioned ^3.0 and vendor-plugin ^3.0.

### Removed

- **The provider interfaces** (`AdminToolbar*ProviderInterface`, `AdminToolbarJavascriptProviderInterface`, `AdminToolbarStylesheetProviderInterface`) and the `AdminToolbar*Interface` model contracts. Use the component bases and their `javascript` / `stylesheets` config.
- **The `disabled_menus`, `disabled_menu_items`, `disabled_buttons` and `disabled_toggles` config.** See Upgrading.
- **The Elemental Grid menu.** It reached into `wedevelopnl/silverstripe-elemental-grid`, an SS5 module. Its SS6 successor `wedevelopnl/silverstripe-grid` will ship its own Grid menu as an ordinary toolbar component, so the toolbar no longer needs a release for every Grid change.
- **The "Add to campaign" menu item.** It linked to `#` and did nothing.
- **`URLTranslator`.** Components use `getCMSEditLink()` and the CMS's own link helpers.

### Fixed

- **Page actions failed on a site installed in a subdirectory.** The script posted to a hard-coded `/admintoolbaraction/pageAction`. The endpoint is now built from `Director::baseURL()` and passed to the script through the template.
- **A page action that failed did nothing visible.** Errors were only written to the browser console. See Added.
- **Spacing between toolbar buttons and between menu entries.** The CSS reset loaded after the utilities and zeroed every `space-x`/`space-y` margin, so buttons and dialog lists sat edge to edge. Labelled buttons now get 12px after their text while icon-only buttons stay square.

### Security

- **The 2.x page-action endpoint unpublished or archived any page for anyone holding a valid CSRF token.** `admintoolbaraction/pageAction` checked the token and nothing else. It never checked that the requester was logged in, held `ADMIN_TOOLBAR`, or had permission to unpublish or delete that page, and it acted on whichever page ID the body named. The endpoint now requires a logged-in member with `ADMIN_TOOLBAR` and the page-level permission for the action (see Changed), accepts only POST, and logs every denial. **2.0.6 and earlier are affected.**

### Accessibility

- The collapse button reports its state through `aria-expanded` / `aria-controls`. It and the toggles button carry an `aria-label`, and every dialog is labelled. Decorative icons are `aria-hidden`.
- Every toolbar control is a `<button type="button">`, so none of them submits a surrounding form.
- Dialogs open as modal `<dialog>` elements, close on Escape and on a backdrop click, and only react to triggers inside the toolbar, so a host site's own dialogs are left alone.
- A page-action button is disabled while its request is in flight, and the flush-cache button is marked `aria-busy`.

### Developer Experience

- Docker test harness on Silverstripe 6 (PHP 8.3–8.5 matrix) driven by Task, with a committed Composer lock, `task verify-lock` and a weekly relock pull request.
- PHPUnit `unit`, `integration` and `functional` suites with a 90% coverage gate, PHPStan at `level: max` with no baseline, Rector, Infection (MSI ≥ 99) and class-leak.
- TypeScript sources with Vitest (coverage gate), Biome, `tsc` and Stryker. Vite and the Tailwind CLI build `client/dist`, and CI fails when the committed build output drifts from the sources.
- Playwright end-to-end suite in Chromium and Firefox, with fixtures loaded through `wedevelopnl/silverstripe-e2e`.
- GitHub Actions run static analysis, the PHP and JS suites and the frontend build. A pre-push hook runs the same QA gate locally.
- The README documents installation, who sees the toolbar, and how a module adds or removes a component.

[6.0.0-rc.1]: https://github.com/wedevelopnl/silverstripe-admintoolbar/releases/tag/6.0.0-rc.1
