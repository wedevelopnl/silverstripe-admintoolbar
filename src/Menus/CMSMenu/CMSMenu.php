<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\CMSMenu;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\ORM\ArrayList;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenu;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuProviderInterface;
use WeDevelop\AdminToolbar\Menus\CMSMenu\MenuItems\CMSMenuItem;

class CMSMenu extends AdminToolbarMenu implements AdminToolbarMenuProviderInterface
{
    private static int $order = 0;

    public const MENU_NAME = 'CMSMenu';

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
        return 'font-icon-menu';
    }

    public function provideAdminToolbarMenu(): ?AdminToolbarMenu
    {
        return self::create();
    }

    public function isMenuSupported(): bool
    {
        return true;
    }

    public function getItems(): ArrayList
    {
        $menuItems = [
            CMSMenuItem::create()->setCustomHTML("<b>CMS Menu</b>"),
        ];

        foreach (LeftAndMain::create()->MainMenu() as $mainMenuItem) {
            $item = CMSMenuItem::create();

            $title = $mainMenuItem->getField('Title');
            $link = $mainMenuItem->getField('Link');
            $iconClass = $mainMenuItem->getField('IconClass');

            $item->setCustomHTML("<a href=\"$link\" class=\"$iconClass\" target=\"_blank\">$title</a>");

            $menuItems[] = $item;
        }

        return ArrayList::create($menuItems);
    }
}
