<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menu\User;

use SilverStripe\Admin\CMSProfileController;
use WeDevelop\AdminToolbar\Menu\UserMenu;
use WeDevelop\AdminToolbar\Model\MenuItem;

class EditUserItem extends MenuItem
{
    private static string $menu = UserMenu::class;

    private static int $order = 20;

    private static string $title = 'Edit profile';

    private static string $icon = 'font-icon-edit';

    public function isSupported(): bool
    {
        return (bool) CMSProfileController::singleton()->canView($this->getContext()->member);
    }

    public function getLink(): ?string
    {
        return CMSProfileController::singleton()->Link();
    }
}
