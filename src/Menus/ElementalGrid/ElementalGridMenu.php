<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\ElementalGrid;

use Composer\InstalledVersions;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use WeDevelop\AdminToolbar\Menus\ElementalGrid\MenuItems\ElementalGridMenuItem;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenu;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuProviderInterface;

class ElementalGridMenu extends AdminToolbarMenu implements AdminToolbarMenuProviderInterface
{
    private int $order = 2;

    public const MENU_NAME = 'ElementalGrid';

    public function getName(): string
    {
        return self::MENU_NAME;
    }

    public function getTitle(): ?string
    {
        return _t('AdminToolbar.GRID', 'Grid');
    }

    public function getHTML(): string
    {
        if (!Controller::has_curr() || !($controller = Controller::curr()) instanceof ContentController) {
            return '';
        }

        $page = $controller->data();

        $elements = $page->ElementalArea?->Elements() ?? [];

        return count($elements) . ' elements';
    }

    public function getIcon(): string
    {
        return 'font-icon-block-layout';
    }

    public function provideAdminToolbarMenu(): AdminToolbarMenu
    {
        return self::create();
    }

    public function isMenuSupported(): bool
    {
        if (!Controller::has_curr() || !($controller = Controller::curr()) instanceof ContentController) {
            return false;
        }

        $page = $controller->data();

        if (
            (
                InstalledVersions::isInstalled('wedevelopnl/silverstripe-elemental-grid')
                && $page->hasExtension(\WeDevelop\ElementalGrid\Extensions\ElementalPageExtension::class)
                && $page->UseElementalGrid
            )
            ||
            (
                InstalledVersions::isInstalled('dnadesign/silverstripe-elemental')
                && $page->hasExtension(\DNADesign\Elemental\Extensions\ElementalPageExtension::class)
            )
        ) {
            return $page->ElementalArea()->Elements()->count() > 0;
        }

        return false;
    }

    public function getElementalConfig(): \WeDevelop\ElementalGrid\ElementalConfig
    {
        return new \WeDevelop\ElementalGrid\ElementalConfig();
    }

    public function getItems(): ArrayList
    {
        if (!Controller::has_curr() || !($controller = Controller::curr()) instanceof ContentController) {
            return ArrayList::create();
        }

        $page = $controller->data();

        $menuItems = [];

        $elements = $page->ElementalArea()->Elements() ?? [];

        /** @var \DNADesign\Elemental\Models\BaseElement $element */
        foreach ($elements as $element) {
            $item = ElementalGridMenuItem::create();

            $sizeKey = sprintf('Size%s', \WeDevelop\ElementalGrid\ElementalConfig::getDefaultViewport());
            $element->Title = $element->getTitle();
            $element->Size = $element->$sizeKey ?: 12;
            $item->setElement($element);

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
