<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\Page\MenuItems;

use SilverStripe\Security\Security;
use SilverStripe\Versioned\Versioned;
use WeDevelop\AdminToolbar\Menus\Page\PageMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;
use SilverStripe\Control\Controller;
use WeDevelop\AdminToolbar\URLTranslator;

class StageMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public function getHTML(): string
    {
        $page = Controller::curr()->record;

        /** @var Versioned $version */
        $version = Versioned::get_version($page['ClassName'], $page['ID'], $page['Version']);

        $isPublished = $version->isPublished();
        $isArchived = $version->isArchived();
        $isDraft = $version->isModifiedOnDraft();

        $state = 'UNKNOWN';

        if ($isPublished) {
            $state = 'Published';
        }

        if ($isArchived) {
            $state = 'Archived';
        }

        if ($isDraft) {
            $state = 'Draft';
        }

        return 'State: ' . $state;
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
