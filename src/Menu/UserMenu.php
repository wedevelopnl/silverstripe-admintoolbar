<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Menu;

use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use WeDevelop\AdminToolbar\Model\Menu;

class UserMenu extends Menu
{
    private static string $title = 'User';

    private static string $icon = 'font-icon-torso';

    private static string $placement = self::PLACEMENT_END;

    public function getMember(): Member
    {
        return $this->getContext()->member;
    }

    public function getLogoutLink(): string
    {
        return Security::logout_url();
    }
}
