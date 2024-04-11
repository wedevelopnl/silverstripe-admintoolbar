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

class UnpublishMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public const ACTION = 'unpublish';

    public function getName(): string
    {
        return 'UnpublishMenuItem';
    }

    public function getTitle(): string
    {
        return _t('Page.UNPUBLISH', 'Unpublish');
    }

    public function getLink(): ArrayData
    {
        if (!Controller::has_curr() || !($controller = Controller::curr()) instanceof ContentController) {
            return ArrayData::create();
        }

        $page = $controller->data();

        return ArrayData::create([
            'PageId' => $page->ID,
            'ExtraClasses' => 'ss-at-text-black hover:ss-at-text-primary',
            'Action' => self::ACTION,
        ]);
    }

    public function getIcon(): string
    {
        return 'font-icon-eye-with-line';
    }

    public function isMenuItemSupported(): bool
    {
        if (!Controller::has_curr() || !($controller = Controller::curr()) instanceof ContentController) {
            return false;
        }

        $page = $controller->data();

        return $page->canUnpublish() && $page->isPublished();
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
        return 3;
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }
}
