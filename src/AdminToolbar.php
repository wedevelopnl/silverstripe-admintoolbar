<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar;

use SilverStripe\Admin\AdminRootController;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Model\ModelData;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\View\Requirements;
use WeDevelop\AdminToolbar\Model\Button;
use WeDevelop\AdminToolbar\Model\Component;
use WeDevelop\AdminToolbar\Model\Menu;
use WeDevelop\AdminToolbar\Model\Toggle;

class AdminToolbar extends ModelData implements PermissionProvider
{
    public const string PERMISSION = 'ADMIN_TOOLBAR';

    public function render(ToolbarContext $context): DBHTMLText
    {
        Requirements::css('wedevelopnl/silverstripe-admintoolbar:client/dist/css/toolbar.css');
        Requirements::javascript('wedevelopnl/silverstripe-admintoolbar:client/dist/js/toolbar.js');

        $menus = Component::discover(Menu::class, $context);

        return $this->customise([
            'StartMenus' => ArrayList::create($this->menusAt($menus, Menu::PLACEMENT_START)),
            'EndMenus' => ArrayList::create($this->menusAt($menus, Menu::PLACEMENT_END)),
            'Buttons' => ArrayList::create(Component::discover(Button::class, $context)),
            'Toggles' => ArrayList::create(Component::discover(Toggle::class, $context)),
        ])->renderWith(self::class);
    }

    public function getAdminURL(): string
    {
        return AdminRootController::admin_url();
    }

    public function getCMSVersion(): string
    {
        return LeftAndMain::singleton()->CMSVersionNumber();
    }

    /**
     * @return array<string, string>
     */
    public function providePermissions(): array
    {
        return [
            self::PERMISSION => _t(self::class . '.PERMISSION', 'Use the admin toolbar'),
        ];
    }

    /**
     * @param list<Menu> $menus
     * @return array<Menu>
     */
    private function menusAt(array $menus, string $placement): array
    {
        return array_filter($menus, static fn (Menu $menu): bool => $menu->getPlacement() === $placement);
    }
}
