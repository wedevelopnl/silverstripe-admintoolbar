<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\CMSMenu\MenuItems;

use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ArrayData;
use WeDevelop\AdminToolbar\Menus\CMSMenu\CMSMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;

class CMSMenuItem extends AdminToolbarMenuItem
{
    private ArrayData $menuItem;

    public function getName(): string
    {
        return 'CMSMenuItem';
    }

    public function isMenuItemSupported(): bool
    {
        return true;
    }

    public function isForMenu(string $menuName): bool
    {
        return $menuName === CMSMenu::MENU_NAME;
    }

    public function getOrder(): int
    {
        return 0;
    }

    public function setMenuItem(ArrayData $menuItem): self
    {
        $this->menuItem = $menuItem;

        return $this;
    }

    public function getMenuItem(): ArrayData
    {
        return $this->menuItem;
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }
}
