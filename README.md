# Silverstripe Admin Toolbar

A toolbar on the frontend of a Silverstripe CMS site for logged-in CMS users: page actions (edit, unpublish, archive), the CMS main menu, a user menu, cache flush, and query/timing diagnostics.

## Requirements

* PHP ^8.3
* Silverstripe CMS ^6.0

## Installation

```
composer require wedevelopnl/silverstripe-admintoolbar
```

Place `$AdminToolbar` in your page template, usually just before `</body>` in `Page.ss`:

```
$AdminToolbar
```

## Who sees the toolbar

* Members need the **Use the admin toolbar** permission (`ADMIN_TOOLBAR`). Administrators have it implicitly.
* Each member can switch the toolbar off, or start it collapsed, on their profile.
* The toolbar is never rendered in the CMS preview, or when the URL contains `?AdminToolbarDisabled=1`.

## Removing a component

The toolbar is made of components: menus, menu items, buttons and toggles. Every component can be switched off in YAML:

```yaml
WeDevelop\AdminToolbar\Button\FlushCacheButton:
  enabled: false
```

In `live` environments the Queries button and toggle are disabled by default.

## Adding a component from your module or project

The toolbar discovers components by type. Declaring a concrete subclass of one of the bases in `WeDevelop\AdminToolbar\Model` adds it; nothing needs registering.

| Base | Renders as |
|---|---|
| `Menu` | A toolbar button that opens a dialog listing its items |
| `MenuItem` | An entry in a menu's dialog |
| `Button` | A button in the toolbar |
| `Toggle` | A checkbox in the toolbar |

### A menu with items

```php
namespace App\AdminToolbar;

use WeDevelop\AdminToolbar\Model\Menu;

class ReportsMenu extends Menu
{
    private static string $title = 'Reports';

    private static string $icon = 'font-icon-chart-line';

    private static int $order = 30;

    public function isSupported(): bool
    {
        return $this->getContext()->page !== null;
    }
}
```

```php
namespace App\AdminToolbar;

use WeDevelop\AdminToolbar\Model\MenuItem;

class BrokenLinksItem extends MenuItem
{
    private static string $menu = ReportsMenu::class;

    private static string $title = 'Broken links';

    public function getLink(): ?string
    {
        return '/admin/reports/broken-links';
    }
}
```

### Configuration

| Config | Applies to | Meaning |
|---|---|---|
| `title` | all | English label; translated through `<FQCN>.TITLE` in your `lang/*.yml` |
| `icon` | all | An icon class from the CMS icon font, e.g. `font-icon-edit` |
| `order` | all | Position among components of the same type, lowest first (built-in menus use 10 and 20) |
| `enabled` | all | `false` leaves the component out |
| `placement` | `Menu` | `start` (default) left of the buttons, `end` right of the toggles |
| `menu` | `MenuItem` | Class of the menu the item belongs to; a subclass of that menu keeps the item |
| `javascript`, `stylesheets` | all | Module resource paths loaded when the component renders, e.g. `vendor/module:client/dist/menu.css` |

### Behaviour

* Return `false` from `isSupported()` to leave the component out for the current request; a menu with nothing to show should do this.
* Read the current page, member and request from `$this->getContext()` (`page` is `null` outside a page), never from `Controller::curr()` or `Security::getCurrentUser()`.

### Templates and styling

A component renders with the template matching its class name, falling back to the template of its base. `App\AdminToolbar\ReportsMenu` looks for `templates/App/AdminToolbar/ReportsMenu.ss`, then uses the toolbar's `WeDevelop/AdminToolbar/Model/Menu.ss`.

The toolbar's stylesheet holds only the utility classes (`ssat:` prefix) its own templates use. A custom template that needs other styles must ship them through the component's `stylesheets` config.

## Development

The test harness runs in Docker and is driven by [Task](https://taskfile.dev): `task up` starts it, `task --list` shows every command.
