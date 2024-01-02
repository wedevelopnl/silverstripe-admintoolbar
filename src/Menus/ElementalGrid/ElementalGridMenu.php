<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\ElementalGrid;

use Composer\InstalledVersions;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use WeDevelop\AdminToolbar\Menus\CMSMenu\MenuItems\CMSMenuItem;
use WeDevelop\AdminToolbar\Menus\ElementalGrid\MenuItems\ElementalGridMenuItem;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenu;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuProviderInterface;

class ElementalGridMenu extends AdminToolbarMenu implements AdminToolbarMenuProviderInterface
{
    public const MENU_NAME = 'ElementalGrid';
    private const ELEMENTAL_GRID_PAGE_EXTENSION_CLASS = 'DNADesign\\Elemental\\Extensions\\ElementalPageExtension';
    private static int $order = 11;

    public function getName(): string
    {
        return self::MENU_NAME;
    }

    public function getHTML(): string
    {
        $page = Controller::curr()?->data();
        $elements = $page?->ElementalArea?->Elements();

        // @TODO: Empty out and move count to a badge
        return count($elements) . ' elements';
    }

    public function getIcon(): string
    {
        return 'font-icon-block-layout';
    }

    public function provideAdminToolbarMenu(): ?AdminToolbarMenu
    {
        return self::create();
    }

    public function isMenuSupported(): bool
    {
        $page = Controller::curr()->data();

        return (InstalledVersions::isInstalled('dnadesign/silverstripe-elemental') && $page->hasExtension(self::ELEMENTAL_GRID_PAGE_EXTENSION_CLASS));
    }

    public function getItems(): ArrayList
    {
        $page = Controller::curr()?->data();
        $menuItems = [
            CMSMenuItem::create()->setCustomHTML("<b>Elemental Grid</b>"),
        ];

        $elements = $page?->ElementalArea?->Elements();

        foreach ($elements as $element) {
            $item = ElementalGridMenuItem::create();

            $title = $element->getTitle() ?? 'Untitled ' . $element->i18n_singular_name();
            $editLink = $element->getEditLink();
            $anchor = $element->getAnchor();

            $item->setCustomHTML("<a href=\"#$anchor\">$title <a href=\"$editLink\" target=\"_blank\"><i class=\"font-icon-edit\"></i>Edit</a></a>");
            $menuItems[] = $item;
        }

        return ArrayList::create($menuItems);
    }
}
