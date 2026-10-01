<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Model;

abstract class MenuItem extends Component
{
    /** @var class-string<Menu>|'' the menu this item belongs to (also attaches to its subclasses) */
    private static string $menu = '';

    public function getMenu(): string
    {
        /** @var string $menu */
        $menu = static::config()->get('menu');

        return $menu;
    }

    public function getLink(): ?string
    {
        return null;
    }
}
