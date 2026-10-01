<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Model;

abstract class MenuItem extends Component
{
    /** Class name of the Menu this item belongs to (it also attaches to that menu's subclasses); '' for none. */
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
