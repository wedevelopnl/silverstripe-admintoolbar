<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ViewableData;

abstract class AdminToolbarMenuItem extends ViewableData implements AdminToolbarMenuItemInterface
{
    use Configurable;

    /** @config */
    private static int $order = 10;

    public static string $forMenu = '';

    public function getExtraClasses(): string
    {
        return '';
    }

    public function isSubMenu(): bool
    {
        return false;
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }

    public function getSubMenu(): ?AdminToolbarMenu
    {
        return null;
    }

    public function getOrder(): int
    {
        return static::config()->get('order') ?? self::$order;
    }
}
