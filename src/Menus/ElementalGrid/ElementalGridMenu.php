<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\ElementalGrid;

use Composer\InstalledVersions;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\SiteConfig\SiteConfig;
use WeDevelop\AdminToolbar\Menus\CMSMenu\MenuItems\CMSMenuItem;
use WeDevelop\AdminToolbar\Menus\ElementalGrid\MenuItems\ElementalGridMenuItem;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenu;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuProviderInterface;
use WeDevelop\ElementalGrid\CSSFramework\BulmaCSSFramework;
use WeDevelop\ElementalGrid\ElementalConfig;
use WeDevelop\ElementalGrid\Models\ElementRow;

class ElementalGridMenu extends AdminToolbarMenu implements AdminToolbarMenuProviderInterface
{
    private int $order = 2;

    public const MENU_NAME = 'ElementalGrid';

    private const ELEMENTAL_GRID_PAGE_EXTENSION_CLASS = 'DNADesign\\Elemental\\Extensions\\ElementalPageExtension';

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
        $page = Controller::curr()?->data();
        $elements = $page?->ElementalArea?->Elements();

        // @TODO: Empty out and move count to a badge
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
        $page = Controller::curr()->data();

        return (InstalledVersions::isInstalled('dnadesign/silverstripe-elemental') && $page->hasExtension(self::ELEMENTAL_GRID_PAGE_EXTENSION_CLASS));
    }

    public function getElementalConfig(): ElementalConfig
    {
        return new ElementalConfig();
    }

    public function getItems(): ArrayList
    {
        $page = Controller::curr()?->data();

        $menuItems = [];

        $elements = $page?->ElementalArea?->Elements();

        /** @var BaseElement $element */
        foreach ($elements as $element) {
            $item = ElementalGridMenuItem::create();

            $sizeKey = sprintf('Size%s', ElementalConfig::getDefaultViewport());
            $element->Title = $element->getTitle() ?? 'Untitled ' . $element->i18n_singular_name();
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
