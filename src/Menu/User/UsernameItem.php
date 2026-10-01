<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menu\User;

use WeDevelop\AdminToolbar\Menu\UserMenu;
use WeDevelop\AdminToolbar\Model\MenuItem;

class UsernameItem extends MenuItem
{
    private static string $menu = UserMenu::class;

    private static int $order = 10;

    public function getMemberName(): string
    {
        return $this->getContext()->member->getName();
    }
}
