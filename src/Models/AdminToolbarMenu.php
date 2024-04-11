<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;
use SilverStripe\View\ViewableData;
use WeDevelop\AdminToolbar\AdminToolbar;
use WeDevelop\AdminToolbar\Providers\AdminToolbarJavascriptProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarStylesheetProviderInterface;

abstract class AdminToolbarMenu extends ViewableData implements AdminToolbarMenuInterface
{
    use Configurable;

    /** @config */
    private static int $order = 10;

    public function getItems(): ArrayList
    {
        $items = [];
        $menuName = $this->getName();

        foreach(ClassInfo::implementorsOf(AdminToolbarMenuItemProviderInterface::class) as $itemClass) {
            /** @var AdminToolbarMenuItemProviderInterface $inst */
            $inst = $itemClass::create();

            if (
                !$inst->isMenuItemSupported()
                || in_array($inst->provideAdminToolbarMenuItem()->getName(), Config::forClass(AdminToolbar::class)->get('disabled_menu_items') ?? [], true)
            ) {
                continue;
            }

            if ($inst->isForMenu($menuName)) {
                $this->provideJSAndCSSForItem($inst->provideAdminToolbarMenuItem());
                $items[] = $inst;
            }
        }

        usort($items, static fn (AdminToolbarMenuItemProviderInterface $itemA, AdminToolbarMenuItemProviderInterface $itemB) => $itemA->provideAdminToolbarMenuItem()->getOrder() <=> $itemB->provideAdminToolbarMenuItem()->getOrder());

        return ArrayList::create($items);
    }

    public function getExtraClasses(): string
    {
        return '';
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }

    public function getOrder(): int
    {
        return static::config()->get('order') ?? self::$order;
    }

    protected function provideJSAndCSSForItem(AdminToolbarMenuItemInterface $item): void
    {
        if ($item instanceof AdminToolbarJavascriptProviderInterface) {
            $scripts = $item->provideJavascript();

            foreach ($scripts as $script) {
                Requirements::javascript($script);
            }
        }

        if ($item instanceof AdminToolbarStylesheetProviderInterface) {
            $sheets = $item->provideStylesheets();

            foreach ($sheets as $sheet) {
                Requirements::css($sheet);
            }
        }
    }
}
