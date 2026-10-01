<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;

/**
 * @property bool $DisableAdminToolbar
 * @property bool $AdminToolbarDefaultCollapsed
 */
class MemberExtension extends Extension
{
    /**
     * @config
     * @var array<string, string>
     */
    private static array $db = [
        'DisableAdminToolbar' => 'Boolean',
        'AdminToolbarDefaultCollapsed' => 'Boolean',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        /** @var Member|MemberExtension $currentUser */
        $currentUser = Security::getCurrentUser();

        if (!$currentUser->canHaveAdminToolbar()) {
            $fields->removeByName('DisableAdminToolbar');
            $fields->removeByName('AdminToolbarDefaultCollapsed');
        }
    }

    public function canHaveAdminToolbar(): bool|int
    {
        return Permission::check('ADMIN_TOOLBAR');
    }

    public function showAdminToolbar(): bool
    {
        return !$this->getOwner()->DisableAdminToolbar;
    }

}
