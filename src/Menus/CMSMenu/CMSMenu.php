<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\CMSMenu;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ArrayData;
use WeDevelop\AdminToolbar\Menus\CMSMenu\MenuItems\CMSMenuItem;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenu;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuProviderInterface;

class CMSMenu extends AdminToolbarMenu implements AdminToolbarMenuProviderInterface
{
    private int $order = 1;

    public const MENU_NAME = 'CMSMenu';

    public function getName(): string
    {
        return self::MENU_NAME;
    }

    public function getTitle(): ?string
    {
        return _t('AdminToolbar.MENU', 'Menu');
    }

    public function getHTML(): string
    {
        return ' ';
    }

    public function getIcon(): string
    {
        return 'font-icon-menu';
    }

    public function provideAdminToolbarMenu(): AdminToolbarMenu
    {
        return self::create();
    }

    public function isMenuSupported(): bool
    {
        return true;
    }

    public function getItems(): ArrayList
    {
        /** @var ArrayList<ArrayData> $menus */
        $menus = LeftAndMain::create()->MainMenu();

        $menuItems = [];

        foreach ($menus as $menu) {
            $item = CMSMenuItem::create();
            $item->setMenuItem($menu);

            $menuItems[] = $item;
        }

        return ArrayList::create($menuItems);
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }

    public function getOrder(): int
    {
        return $this->order;
    }
}
