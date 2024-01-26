<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\User\MenuItems;

use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Security;
use WeDevelop\AdminToolbar\Menus\User\UserMenu;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenuItem;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuItemProviderInterface;
use WeDevelop\AdminToolbar\URLTranslator;

class EditUserMenuItem extends AdminToolbarMenuItem implements AdminToolbarMenuItemProviderInterface
{
    public function getEditLink(): string
    {
        $member = UserMenu::getCurrentMember();
        $url = URLTranslator::getUserEditURL($member);

        return $url;
    }

    public function isMenuItemSupported(): bool
    {
        return Security::getCurrentUser() !== null;
    }

    public function provideAdminToolbarMenuItem(): AdminToolbarMenuItem
    {
        return self::create();
    }

    public function isForMenu(string $menuName): bool
    {
        return $menuName === UserMenu::MENU_NAME;
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }
}
