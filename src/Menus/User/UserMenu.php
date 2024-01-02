<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\User;

use SilverStripe\Security\Security;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenu;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuProviderInterface;

class UserMenu extends AdminToolbarMenu implements AdminToolbarMenuProviderInterface
{
    private static int $order = 9;
    public const MENU_NAME = 'User';

    public function getName(): string
    {
        return self::MENU_NAME;
    }

    public function getHTML(): string
    {
        return ' ';
    }

    public function getIcon(): string
    {
        return 'font-icon-torso';
    }

    public function provideAdminToolbarMenu(): ?AdminToolbarMenu
    {
        return self::create();
    }

    public function isMenuSupported(): bool
    {
        return true;
    }
}
