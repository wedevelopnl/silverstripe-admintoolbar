<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\Page\MenuItems;

use SilverStripe\Versioned\Versioned;
use WeDevelop\AdminToolbar\Menus\Page\PageMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;
use SilverStripe\Control\Controller;
use WeDevelop\AdminToolbar\URLTranslator;

class LastEditedByMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public function getHTML(): string
    {
        $page = Controller::curr()->record;
        $version = Versioned::get_version($page['ClassName'], $page['ID'], $page['Version']);
        $author = $version->Author();
        $editLink = URLTranslator::getUserEditURL($author);

        return "<a href=\"$editLink\" target=\"_blank\">Last Edited By: {$author->getName()}</a>";
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
        return $menuName === PageMenu::MENU_NAME;
    }
}
