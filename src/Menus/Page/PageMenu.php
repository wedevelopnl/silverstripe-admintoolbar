<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\Page;

use WeDevelop\AdminToolbar\Models\AdminToolbarMenu;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuProviderInterface;

class PageMenu extends AdminToolbarMenu implements AdminToolbarMenuProviderInterface
{
    public const MENU_NAME = 'Page';

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
        return 'font-icon-page-multiple';
    }

    public function provideAdminToolbarMenu(): AdminToolbarMenu
    {
        return self::create();
    }

    public function isMenuSupported(): bool
    {
        return true;
    }
}
