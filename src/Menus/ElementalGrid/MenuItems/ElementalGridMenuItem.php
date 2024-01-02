<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\ElementalGrid\MenuItems;

use WeDevelop\AdminToolbar\Menus\CMSMenu\CMSMenu;
use WeDevelop\AdminToolbar\Menus\ElementalGrid\ElementalGridMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;

class ElementalGridMenuItem extends AdminToolbarMenuItem
{
    private string $customHTML;

    public function isMenuItemSupported(): bool
    {
        return true;
    }

    public function isForMenu(string $menuName): bool
    {
        return $menuName === ElementalGridMenu::MENU_NAME;
    }

    public function getOrder(): int
    {
        return 0;
    }

    public function getHTML(): string
    {
        return $this->customHTML;
    }

    public function setCustomHTML(string $html): self
    {
        $this->customHTML = $html;

        return $this;
    }
}
