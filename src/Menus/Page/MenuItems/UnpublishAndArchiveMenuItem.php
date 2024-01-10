<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\Page\MenuItems;

use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;
use WeDevelop\AdminToolbar\Menus\Page\PageMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;
use SilverStripe\Control\Controller;
use WeDevelop\AdminToolbar\URLTranslator;

class UnpublishAndArchiveMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public function getTitle(): string
    {
        return "Unpublish and archive";
    }

    public function getLink(): ArrayData
    {
        return ArrayData::create([
            'LinkURL' => '#',
            'ExtraClasses' => 'ss-at-text-red-600 hover:ss-at-text-red-700'
        ]);
    }

    public function getIcon(): string
    {
        return 'font-icon-trash';
    }

    public function isMenuItemSupported(): bool
    {
        return true;
    }

    public function provideAdminToolbarMenuItem(): AdminToolbarMenuItem
    {
        return self::create();
    }

    public function isForMenu(string $menuName): bool
    {
        return $menuName === PageMenu::MENU_NAME;
    }

    public function getOrder(): int
    {
        return 4;
    }
}
