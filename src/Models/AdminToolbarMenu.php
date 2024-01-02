<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Requirements;
use SilverStripe\View\ViewableData;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarJavascriptProviderInterface;
use WeDevelop\AdminToolbar\Providers\AdminToolbarStylesheetProviderInterface;

abstract class AdminToolbarMenu extends ViewableData implements AdminToolbarMenuInterface
{
    use Configurable;

    private static int $order = 10;

    public function getItems(): ArrayList
    {
        $items = [];
        $menuName = $this->getName();

        foreach(ClassInfo::implementorsOf(AdminToolbarMenuItemProviderInterface::class) as $itemClass)
        {
            /** @var AdminToolbarMenuItemProviderInterface $inst */
            $inst = $itemClass::create();

            if ($inst->isForMenu($menuName)) {
                $this->provideJSAndCSSForItem($inst->provideAdminToolbarMenuItem());
                $items[] = $inst;
            }
        }

        usort($items, static function(
            AdminToolbarMenuItemProviderInterface $itemA,
            AdminToolbarMenuItemProviderInterface $itemB,
        ) {
            return $itemA->getOrder() <=> $itemB->getOrder();
        });

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
