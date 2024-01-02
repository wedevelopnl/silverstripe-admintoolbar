<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\CMSMenu\MenuItems;

use SilverStripe\Security\Security;
use WeDevelop\AdminToolbar\Menus\CMSMenu\CMSMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;
use SilverStripe\Control\Controller;
use WeDevelop\AdminToolbar\URLTranslator;

class CMSMenuItem extends AdminToolbarMenuItem
{
    private string $customHTML;

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
