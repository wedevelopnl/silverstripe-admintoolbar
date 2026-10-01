<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Models;

use SilverStripe\Model\ModelData;
use SilverStripe\Core\Config\Configurable;

abstract class AdminToolbarMenuItem extends ModelData implements AdminToolbarMenuItemInterface
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

    public function forTemplate(): string
    {
        return $this->renderWith(self::class)->forTemplate();
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
