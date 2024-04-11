<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menus\User;

use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use WeDevelop\AdminToolbar\Models\AdminToolbarMenu;
use WeDevelop\AdminToolbar\Providers\AdminToolbarMenuProviderInterface;

class UserMenu extends AdminToolbarMenu implements AdminToolbarMenuProviderInterface
{
    /** @config */
    private static int $order = 9;

    public const MENU_NAME = 'User';

    public function getName(): string
    {
        return self::MENU_NAME;
    }

    public function getTitle(): ?string
    {
        return _t('AdminToolbar.USER', 'User');
    }

    public function getHTML(): string
    {
        return ' ';
    }

    public function getIcon(): string
    {
        return 'font-icon-torso';
    }

    public function provideAdminToolbarMenu(): AdminToolbarMenu
    {
        return self::create();
    }

    public function isMenuSupported(): bool
    {
        return false;
    }

    public function forTemplate(): DBHTMLText
    {
        return $this->renderWith(self::class);
    }

    public static function getCurrentMember(): ?Member
    {
        return Security::getCurrentUser();
    }

    public function getLogoutLink(): string
    {
        return Security::logout_url();
    }
}
