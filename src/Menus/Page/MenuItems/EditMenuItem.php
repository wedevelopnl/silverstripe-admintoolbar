<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\Page\MenuItems;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\View\ArrayData;
use WeDevelop\AdminToolbar\Menus\Page\PageMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;
use WeDevelop\AdminToolbar\URLTranslator;

class EditMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public function getName(): string
    {
        return 'EditPage';
    }

    public function getTitle(): string
    {
        return _t('Page.EDIT', 'Edit page');
    }

    public function getLink(): ArrayData
    {
        if (!Controller::has_curr() || !($controller = Controller::curr()) instanceof ContentController) {
            return ArrayData::create();
        }

        return ArrayData::create([
            'LinkURL' => URLTranslator::getPageEditURL($controller->data()),
            'ExtraClasses' => 'ss-at-text-primary hover:ss-at-text-primary',
        ]);
    }

    public function getIcon(): string
    {
        return 'font-icon-edit';
    }

    public function isMenuItemSupported(): bool
    {
        return true;
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
        return 1;
    }
}
