<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\User\MenuItems;

use SilverStripe\Security\Security;
use WeDevelop\AdminToolbar\Menus\User\UserMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;
use WeDevelop\AdminToolbar\URLTranslator;

class EditUserMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public function getHTML(): string
    {
        $member = Security::getCurrentUser();
        $url = URLTranslator::getUserEditURL($member);

        return "<a href=\"$url\" target=\"_blank\">Edit</a>";
    }

    public function isMenuItemSupported(): bool
    {
        return Security::getCurrentUser() !== null;
    }

    public function provideAdminToolbarMenuItem(): ?AdminToolbarMenuItem
    {
        return new self();
    }

    public function isForMenu(string $menuName): bool
    {
        return $menuName === UserMenu::MENU_NAME;
    }
}
