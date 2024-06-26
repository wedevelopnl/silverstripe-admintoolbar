<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\Page\MenuItems;

use SilverStripe\View\ArrayData;
use WeDevelop\AdminToolbar\Menus\Page\PageMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;

class AddToCampaignMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public function getName(): string
    {
        return 'AddToCampaign';
    }

    public function getTitle(): string
    {
        return "Add to campaign";
    }

    public function getLink(): ArrayData
    {
        return ArrayData::create([
            'LinkURL' => '#',
            'ExtraClasses' => 'ss-at-text-primary hover:ss-at-text-primary',
        ]);
    }

    public function getIcon(): string
    {
        return 'font-icon-rocket';
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
        return 2;
    }
}
