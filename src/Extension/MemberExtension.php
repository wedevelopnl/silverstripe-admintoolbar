<?php

declare(strict_types=1);

namespace WeDevelop\AdminToolbar\Extension;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use WeDevelop\AdminToolbar\AdminToolbar;

/**
 * Per-member toolbar settings.
 *
 * @property bool $DisableAdminToolbar
 * @property bool $AdminToolbarDefaultCollapsed
 * @extends Extension<Member>
 */
class MemberExtension extends Extension
{
    /** @var array<string, string> */
    private static array $db = [
        'DisableAdminToolbar' => 'Boolean',
        'AdminToolbarDefaultCollapsed' => 'Boolean',
    ];

    /** The settings only mean something to a member who can see the toolbar. */
    protected function updateCMSFields(FieldList $fields): void
    {
        if (!Permission::checkMember($this->getOwner(), AdminToolbar::PERMISSION)) {
            $fields->removeByName(['DisableAdminToolbar', 'AdminToolbarDefaultCollapsed']);
        }
    }
}
