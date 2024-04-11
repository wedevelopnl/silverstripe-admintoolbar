<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\Page\MenuItems;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ArrayData;
use WeDevelop\AdminToolbar\Menus\Page\PageMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;

class ArchiveMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public const ACTION = 'archive';

    public function getName(): string
    {
        return 'ArchivePage';
    }

    public function getTitle(): string
    {
        return "Archive";
    }

    public function getLink(): ArrayData
    {
        if (!Controller::has_curr() || !($controller = Controller::curr()) instanceof ContentController) {
            return ArrayData::create();
        }

        $page = $controller->data();

        return ArrayData::create([
            'PageId' => $page->ID,
            'ExtraClasses' => 'ss-at-text-red-600 hover:ss-at-text-red-700',
            'Action' => self::ACTION,
        ]);
    }

    public function getIcon(): string
    {
        return 'font-icon-trash';
    }

    public function isMenuItemSupported(): bool
    {
        if (!Controller::has_curr() || !($controller = Controller::curr()) instanceof ContentController) {
            return false;
        }

        $page = $controller->data();

        return $page->canArchive() && !$page->isPublished();
    }

    public function provideAdminToolbarMenuItem(): AdminToolbarMenuItem
    {
        return self::create();
    }

    public function isForMenu(string $menuName): bool
    {
        return $menuName === PageMenu::MENU_NAME;
    }

    public function getOrder(): int
    {
        return 4;
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }
}
