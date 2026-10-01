<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\User\MenuItems;

use SilverStripe\Security\Member;
use WeDevelop\AdminToolbar\Menus\User\UserMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;

class UsernameMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public function getName(): string
    {
        return 'Username';
    }

    public function getCurrentMember(): ?Member
    {
        return UserMenu::getCurrentMember();
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
        return $menuName === UserMenu::MENU_NAME;
    }

    public function getOrder(): int
    {
        return -1;
    }

    public function forTemplate(): string
    {
        return $this->renderWith(self::class)->forTemplate();
    }
}
