<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\User\MenuItems;

use SilverStripe\Security\Security;
use WeDevelop\AdminToolbar\Menus\User\UserMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;

class UsernameMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public function getHTML(): string
    {
        $username = Security::getCurrentUser()->getName();

        return "<b>$username</b>";
    }

    public function isMenuItemSupported(): bool
    {
        return true;
    }

    public function provideAdminToolbarMenuItem(): ?AdminToolbarMenuItem
    {
        return new self();
    }

    public function isForMenu(string $menuName): bool
    {
        return $menuName === UserMenu::MENU_NAME;
    }

    public function getOrder(): int
    {
        return -1;
    }
}
